<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class RfqSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_rfq_creates_submission_consent_and_audit_event(): void
    {
        $response = $this->from('/')->post('/rfq', [
            'contact_name' => 'Hospitality Buyer',
            'organization_name' => 'Example Hotel',
            'email' => 'BUYER@EXAMPLE.COM',
            'phone' => '+971500000000',
            'service_code' => 'cutlery-restoration',
            'property_type' => 'hotel',
            'urgency' => 'standard',
            'estimated_quantity' => 250,
            'message' => 'Please arrange an assessment.',
            'source_page' => '/',
            'campaign_source' => 'direct',
            'consent' => '1',
            'website' => '',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('rfq_submitted');

        $this->assertDatabaseHas('form_submissions', [
            'type' => 'rfq',
            'status' => 'new',
            'contact_name' => 'Hospitality Buyer',
            'email' => 'buyer@example.com',
            'estimated_quantity' => 250,
        ]);

        $submissionId = DB::table('form_submissions')->value('id');

        $this->assertDatabaseHas('consent_records', [
            'form_submission_id' => $submissionId,
            'purpose' => 'rfq_follow_up',
            'granted' => true,
        ]);

        $this->assertDatabaseHas('audit_events', [
            'action' => 'public.rfq.submitted',
            'subject_id' => $submissionId,
        ]);
    }

    public function test_rfq_requires_explicit_consent(): void
    {
        $response = $this->from('/')->post('/rfq', [
            'contact_name' => 'Hospitality Buyer',
            'email' => 'buyer@example.com',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('consent');
        $this->assertDatabaseCount('form_submissions', 0);
    }

    public function test_honeypot_rejects_automated_submission(): void
    {
        $response = $this->from('/')->post('/rfq', [
            'contact_name' => 'Bot',
            'email' => 'bot@example.com',
            'consent' => '1',
            'website' => 'https://spam.example',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('website');
        $this->assertDatabaseCount('form_submissions', 0);
    }
}
