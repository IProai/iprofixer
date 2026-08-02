<?php

declare(strict_types=1);

use App\Models\Contact;
use App\Models\FormSubmission;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Organization;
use App\Models\User;
use App\Services\DuplicateDetectionService;
use App\Services\LeadService;
use Database\Seeders\CmsPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(CmsPermissionSeeder::class);
});

it('automatically converts RFQ submission to Organization, Contact, and Lead with audit evidence', function (): void {
    $submission = FormSubmission::query()->create([
        'reference' => 'RFQ-TEST-001',
        'type' => 'rfq',
        'status' => 'new',
        'locale' => 'en',
        'contact_name' => 'Jean-Luc Picard',
        'organization_name' => 'Starfleet Hospitality LLC',
        'email' => 'picard@starfleet.org',
        'service_code' => 'cutlery-restoration',
        'correlation_id' => (string) Str::uuid(),
        'submitted_at' => now(),
    ]);

    $leadService = app(LeadService::class);
    $lead = $leadService->createFromRfq($submission);

    expect($lead)->toBeInstanceOf(Lead::class)
        ->and($lead->status)->toBe('new')
        ->and($lead->source)->toBe('rfq')
        ->and($lead->organization->name)->toBe('Starfleet Hospitality LLC')
        ->and($lead->contact->email)->toBe('picard@starfleet.org');

    // Audit event check
    $this->assertDatabaseHas('audit_events', [
        'action' => 'crm.lead.created_from_rfq',
        'subject_type' => Lead::class,
        'subject_id' => (string) $lead->id,
    ]);

    // Idempotency check: creating again returns existing lead
    $secondCallLead = $leadService->createFromRfq($submission);
    expect($secondCallLead->id)->toBe($lead->id);
});

it('detects duplicate organizations by exact and fuzzy name matching', function (): void {
    $created = Organization::query()->create([
        'name' => 'Grand Hyatt Dubai',
        'type' => 'prospect',
    ]);

    expect(Organization::count())->toBe(1);

    $detector = app(DuplicateDetectionService::class);

    $exactMatch = $detector->findDuplicateOrganizations('Grand Hyatt Dubai');
    expect($exactMatch)->not->toBeEmpty()
        ->and($exactMatch->first()->name)->toBe('Grand Hyatt Dubai');

    $fuzzyMatch = $detector->findDuplicateOrganizations('Grand Hyatt Dubai FZ-LLC');
    expect($fuzzyMatch)->not->toBeEmpty()
        ->and($fuzzyMatch->first()->name)->toBe('Grand Hyatt Dubai');

    $noMatch = $detector->findDuplicateOrganizations('Burj Al Arab Hotel');
    expect($noMatch)->toBeEmpty();
});

it('allows authorized sales users to qualify and disqualify leads', function (): void {
    $salesUser = User::factory()->create();
    $salesUser->givePermissionTo(['lead.view', 'lead.qualify']);

    $lead = Lead::factory()->create(['status' => 'new']);

    $response = $this->actingAs($salesUser)->post("/admin/leads/{$lead->id}/qualify", [
        'qualification_notes' => 'Qualified during initial phone discovery.',
    ]);

    $response->assertRedirect();
    expect($lead->fresh()->status)->toBe('qualified')
        ->and($lead->fresh()->qualification_notes)->toBe('Qualified during initial phone discovery.');

    $this->assertDatabaseHas('audit_events', [
        'action' => 'crm.lead.qualified',
        'subject_id' => (string) $lead->id,
    ]);

    // Test disqualifying another lead
    $lead2 = Lead::factory()->create(['status' => 'new']);

    $response2 = $this->actingAs($salesUser)->post("/admin/leads/{$lead2->id}/disqualify", [
        'disqualification_reason' => 'Budget incompatible with quality standard',
    ]);

    $response2->assertRedirect();
    expect($lead2->fresh()->status)->toBe('disqualified')
        ->and($lead2->fresh()->disqualification_reason)->toBe('Budget incompatible with quality standard');
});

it('converts qualified lead to opportunity with full stage provenance', function (): void {
    $salesUser = User::factory()->create();
    $salesUser->givePermissionTo(['lead.view', 'lead.convert', 'opportunity.view']);

    $organization = Organization::factory()->create();
    $contact = Contact::factory()->for($organization)->create();
    $lead = Lead::factory()->create([
        'status' => 'qualified',
        'organization_id' => $organization->id,
        'contact_id' => $contact->id,
    ]);

    $response = $this->actingAs($salesUser)->post("/admin/leads/{$lead->id}/convert", [
        'title' => 'Annual Silverware Care Agreement',
        'estimated_value' => 75000.00,
        'currency_code' => 'AED',
    ]);

    $response->assertRedirect();

    $opportunity = Opportunity::where('lead_id', $lead->id)->firstOrFail();
    expect($opportunity->title)->toBe('Annual Silverware Care Agreement')
        ->and((float) $opportunity->estimated_value)->toBe(75000.00)
        ->and($opportunity->stage)->toBe('discovery')
        ->and($lead->fresh()->status)->toBe('converted');

    $this->assertDatabaseHas('audit_events', [
        'action' => 'crm.lead.converted',
        'subject_id' => (string) $lead->id,
    ]);
});

it('manages opportunity pipeline stages including win/loss audit tracking', function (): void {
    $salesUser = User::factory()->create();
    $salesUser->givePermissionTo(['opportunity.view', 'opportunity.edit']);

    $opportunity = Opportunity::factory()->create(['stage' => 'discovery']);

    // Advance to proposal
    $response = $this->actingAs($salesUser)->post("/admin/opportunities/{$opportunity->id}/stage", [
        'stage' => 'proposal',
        'next_action' => 'Submit versioned quote document',
    ]);

    $response->assertRedirect();
    expect($opportunity->fresh()->stage)->toBe('proposal')
        ->and($opportunity->fresh()->probability)->toBe(60);

    // Record as Lost with mandatory reason
    $responseLost = $this->actingAs($salesUser)->post("/admin/opportunities/{$opportunity->id}/stage", [
        'stage' => 'lost',
        'loss_reason' => 'price',
        'loss_notes' => 'Competitor offered low-quality uncertified polish.',
    ]);

    $responseLost->assertRedirect();
    expect($opportunity->fresh()->stage)->toBe('lost')
        ->and($opportunity->fresh()->loss_reason)->toBe('price');

    $this->assertDatabaseHas('audit_events', [
        'action' => 'crm.opportunity.stage_changed',
        'subject_id' => (string) $opportunity->id,
    ]);
});

it('denies commercial workspace access to unauthorized users', function (): void {
    $guest = User::factory()->create(); // No permissions

    $lead = Lead::factory()->create();

    $this->actingAs($guest)->get('/admin/leads')->assertStatus(403);
    $this->actingAs($guest)->get("/admin/leads/{$lead->id}")->assertStatus(403);
    $this->actingAs($guest)->get('/admin/opportunities')->assertStatus(403);
    $this->actingAs($guest)->get('/admin/organizations')->assertStatus(403);
    $this->actingAs($guest)->get('/admin/contacts')->assertStatus(403);
});
