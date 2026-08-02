<?php

declare(strict_types=1);

use App\Models\FormSubmission;
use App\Notifications\RfqReceivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('stores a valid assessment request with consent, audit and customer notification evidence', function (): void {
    Notification::fake();

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

    Notification::assertSentOnDemand(RfqReceivedNotification::class);
});

it('stores approved RFQ attachments on the private disk with integrity metadata', function (): void {
    Notification::fake();
    Storage::fake('local');

    $response = $this->from('/contact')->post('/rfq', [
        'contact_name' => 'Executive Steward',
        'email' => 'stewarding@example.test',
        'service_code' => 'cutlery-restoration',
        'property_type' => 'hotel',
        'consent' => '1',
        'website' => '',
        'attachments' => [
            UploadedFile::fake()->image('condition.jpg', 1200, 800),
            UploadedFile::fake()->create('inventory.pdf', 240, 'application/pdf'),
        ],
    ]);

    $response->assertRedirect('/contact')->assertSessionHas('rfq_submitted');

    $submission = FormSubmission::query()->sole();
    $attachments = $submission->attachments()->get();

    expect($attachments)->toHaveCount(2)
        ->and($attachments->every(fn ($attachment): bool => $attachment->disk === 'local'))->toBeTrue()
        ->and($attachments->every(fn ($attachment): bool => strlen($attachment->sha256) === 64))->toBeTrue();

    foreach ($attachments as $attachment) {
        Storage::disk('local')->assertExists($attachment->path);
    }
});

it('rejects invalid or automated assessment requests', function (): void {
    Notification::fake();

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
    Notification::assertNothingSent();
});
