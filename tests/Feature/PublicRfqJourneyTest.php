<?php

declare(strict_types=1);

use App\Models\FormSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('stores a valid assessment request with consent and audit evidence', function (): void {
    $response = $this->from('/contact')->post('/rfq', [
        'contact_name' => 'Operations Manager',
        'organization_name' => 'Example Hospitality Group',
        'email' => 'operations@example.test',
        'phone' => '+971500000000',
        'service_code' => 'assessment',
        'property_type' => 'hotel',
        'urgency' => 'priority',
        'estimated_quantity' => 250,
        'message' => 'Please review banquet cutlery condition.',
        'source_page' => 'contact',
        'consent' => '1',
        'website' => '',
    ]);

    $response->assertRedirect('/contact')->assertSessionHas('rfq_submitted');

    $submission = FormSubmission::query()->sole();

    expect($submission->status)->toBe('new')
        ->and($submission->email)->toBe('operations@example.test')
        ->and($submission->service_code)->toBe('assessment')
        ->and($submission->consents()->count())->toBe(1);

    $this->assertDatabaseHas('audit_events', [
        'action' => 'public.rfq.submitted',
        'subject_id' => $submission->getKey(),
    ]);
});

it('rejects invalid or automated assessment requests', function (): void {
    $this->from('/contact')->post('/rfq', [
        'contact_name' => '',
        'email' => 'not-an-email',
        'service_code' => 'invented-service',
        'consent' => null,
        'website' => 'bot-filled-field',
    ])->assertRedirect('/contact')->assertSessionHasErrors([
        'contact_name',
        'email',
        'service_code',
        'consent',
        'website',
    ]);

    $this->assertDatabaseCount('form_submissions', 0);
});
