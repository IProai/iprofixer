<?php

declare(strict_types=1);

use App\Models\FormSubmission;
use App\Models\RfqAttachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

it('denies the RFQ workspace without explicit permission', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/admin/rfqs')->assertForbidden();
});

it('allows an authorised operator to assign and progress an RFQ with audit evidence', function (): void {
    $operator = User::factory()->create();
    $assignee = User::factory()->create();
    $permission = Permission::create(['name' => 'rfq.manage', 'guard_name' => 'web']);
    $operator->givePermissionTo($permission);

    $rfq = FormSubmission::query()->create([
        'reference' => 'RFQ-20260731-ABC123',
        'type' => 'rfq',
        'status' => 'new',
        'locale' => 'en',
        'contact_name' => 'Hotel Operations',
        'email' => 'operations@example.test',
        'correlation_id' => (string) Str::uuid(),
        'submitted_at' => now(),
    ]);

    $this->actingAs($operator)
        ->get("/admin/rfqs/{$rfq->getKey()}")
        ->assertOk()
        ->assertSee('RFQ-20260731-ABC123');

    $this->actingAs($operator)
        ->put("/admin/rfqs/{$rfq->getKey()}", [
            'status' => 'qualified',
            'assigned_to' => $assignee->getKey(),
            'mark_contacted' => true,
        ])
        ->assertRedirect();

    $rfq->refresh();

    expect($rfq->status)->toBe('qualified')
        ->and($rfq->assigned_to)->toBe($assignee->getKey())
        ->and($rfq->last_contacted_at)->not->toBeNull();

    $this->assertDatabaseHas('audit_events', [
        'action' => 'rfq.workflow.updated',
        'subject_id' => (string) $rfq->getKey(),
        'actor_id' => $operator->getKey(),
    ]);
});

it('allows only authorised operators to download an attachment belonging to the RFQ', function (): void {
    Storage::fake('local');

    $operator = User::factory()->create();
    $unauthorisedUser = User::factory()->create();
    $permission = Permission::create(['name' => 'rfq.manage', 'guard_name' => 'web']);
    $operator->givePermissionTo($permission);

    $rfq = FormSubmission::query()->create([
        'reference' => 'RFQ-20260731-FILE01',
        'type' => 'rfq',
        'status' => 'new',
        'locale' => 'en',
        'contact_name' => 'Hotel Operations',
        'email' => 'operations@example.test',
        'correlation_id' => (string) Str::uuid(),
        'submitted_at' => now(),
    ]);

    $path = "rfq/{$rfq->getKey()}/condition.pdf";
    Storage::disk('local')->put($path, 'private-condition-report');

    $attachment = RfqAttachment::query()->create([
        'form_submission_id' => $rfq->getKey(),
        'disk' => 'local',
        'path' => $path,
        'original_name' => 'condition-report.pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 24,
        'sha256' => hash('sha256', 'private-condition-report'),
    ]);

    $url = route('admin.rfqs.attachments.download', [$rfq, $attachment]);

    $this->actingAs($unauthorisedUser)->get($url)->assertForbidden();

    $this->actingAs($operator)
        ->get($url)
        ->assertOk()
        ->assertDownload('condition-report.pdf');

    $this->assertDatabaseHas('audit_events', [
        'action' => 'rfq.attachment.downloaded',
        'subject_id' => (string) $attachment->getKey(),
        'actor_id' => $operator->getKey(),
    ]);
});

it('filters the RFQ inbox by search, status and ownership while preserving authorised access', function (): void {
    $operator = User::factory()->create();
    $owner = User::factory()->create(['name' => 'Commercial Owner']);
    $permission = Permission::create(['name' => 'rfq.manage', 'guard_name' => 'web']);
    $operator->givePermissionTo($permission);

    FormSubmission::query()->create([
        'reference' => 'RFQ-20260731-MATCH1',
        'type' => 'rfq',
        'status' => 'qualified',
        'locale' => 'en',
        'contact_name' => 'Nadia Procurement',
        'organization_name' => 'Crescent Hotel Group',
        'email' => 'nadia@crescent.example',
        'assigned_to' => $owner->getKey(),
        'correlation_id' => (string) Str::uuid(),
        'submitted_at' => now(),
    ]);

    FormSubmission::query()->create([
        'reference' => 'RFQ-20260731-HIDE01',
        'type' => 'rfq',
        'status' => 'new',
        'locale' => 'en',
        'contact_name' => 'Other Customer',
        'organization_name' => 'Different Group',
        'email' => 'other@example.test',
        'assigned_to' => null,
        'correlation_id' => (string) Str::uuid(),
        'submitted_at' => now()->subMinute(),
    ]);

    $this->actingAs($operator)
        ->get(route('admin.rfqs.index', [
            'search' => 'crescent',
            'status' => 'qualified',
            'owner' => $owner->getKey(),
        ]))
        ->assertOk()
        ->assertSee('RFQ-20260731-MATCH1')
        ->assertSee('Crescent Hotel Group')
        ->assertSee('1 request found.')
        ->assertDontSee('RFQ-20260731-HIDE01');

    $this->actingAs($operator)
        ->get(route('admin.rfqs.index', [
            'status' => 'not-a-status',
            'owner' => 'not-a-user',
        ]))
        ->assertOk()
        ->assertSee('RFQ-20260731-MATCH1')
        ->assertSee('RFQ-20260731-HIDE01');

    $this->actingAs($operator)
        ->get(route('admin.rfqs.index', ['owner' => 'unassigned']))
        ->assertOk()
        ->assertSee('RFQ-20260731-HIDE01')
        ->assertDontSee('RFQ-20260731-MATCH1');
});
