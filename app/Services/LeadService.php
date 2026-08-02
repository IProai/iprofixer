<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contact;
use App\Models\FormSubmission;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class LeadService
{
    public function __construct(private readonly DuplicateDetectionService $duplicateDetection) {}

    /**
     * Create organization, contact, and lead from an RFQ FormSubmission.
     * Idempotent: if a lead already exists for this submission, returns it.
     */
    public function createFromRfq(FormSubmission $submission): Lead
    {
        $existing = Lead::query()->where('form_submission_id', $submission->id)->first();
        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($submission): Lead {
            $organization = $this->findOrCreateOrganization($submission);
            $contact = $this->findOrCreateContact($submission, $organization);

            $lead = Lead::query()->create([
                'form_submission_id' => $submission->id,
                'organization_id' => $organization->id,
                'contact_id' => $contact->id,
                'status' => 'new',
                'source' => 'rfq',
                'source_detail' => $submission->source_page,
                'service_code' => $submission->service_code,
                'property_type' => $submission->property_type,
                'urgency' => $submission->urgency,
                'estimated_quantity' => $submission->estimated_quantity,
            ]);

            DB::table('audit_events')->insert([
                'id' => (string) Str::uuid(),
                'action' => 'crm.lead.created_from_rfq',
                'subject_type' => Lead::class,
                'subject_id' => (string) $lead->id,
                'correlation_id' => (string) ($submission->correlation_id ?? Str::uuid()),
                'before' => null,
                'after' => json_encode([
                    'lead_id' => $lead->id,
                    'form_submission_id' => $submission->id,
                    'organization_id' => $organization->id,
                    'contact_id' => $contact->id,
                ], JSON_THROW_ON_ERROR),
                'metadata' => json_encode(['source' => 'rfq_submission'], JSON_THROW_ON_ERROR),
                'occurred_at' => now(),
            ]);

            return $lead;
        });
    }

    /**
     * Qualify a lead and convert it to an Opportunity.
     *
     * @param  array<string, mixed>  $opportunityData
     */
    public function convertToOpportunity(Lead $lead, array $opportunityData, int $actorId): Opportunity
    {
        if ($lead->isConverted()) {
            throw new \LogicException('Lead is already converted to an opportunity.');
        }

        if (! $lead->organization_id) {
            throw new \LogicException('Lead must be linked to an organization before conversion.');
        }

        return DB::transaction(function () use ($lead, $opportunityData, $actorId): Opportunity {
            $lead->update([
                'status' => 'converted',
                'qualified_at' => $lead->qualified_at ?? now(),
                'converted_at' => now(),
                'updated_by' => $actorId,
            ]);

            $opportunity = Opportunity::query()->create([
                'lead_id' => $lead->id,
                'organization_id' => $lead->organization_id,
                'property_id' => $lead->property_id,
                'contact_id' => $lead->contact_id,
                'assigned_to' => $opportunityData['assigned_to'] ?? $lead->assigned_to,
                'title' => $opportunityData['title'],
                'stage' => 'discovery',
                'probability' => $opportunityData['probability'] ?? 20,
                'service_code' => $opportunityData['service_code'] ?? $lead->service_code,
                'estimated_value' => $opportunityData['estimated_value'] ?? null,
                'currency_code' => $opportunityData['currency_code'] ?? 'AED',
                'expected_close_date' => $opportunityData['expected_close_date'] ?? null,
                'next_action' => $opportunityData['next_action'] ?? 'Schedule discovery call',
                'notes' => $opportunityData['notes'] ?? null,
                'created_by' => $actorId,
            ]);

            DB::table('audit_events')->insert([
                'id' => (string) Str::uuid(),
                'actor_id' => $actorId,
                'action' => 'crm.lead.converted',
                'subject_type' => Lead::class,
                'subject_id' => (string) $lead->id,
                'correlation_id' => (string) Str::uuid(),
                'before' => json_encode(['status' => 'qualified'], JSON_THROW_ON_ERROR),
                'after' => json_encode([
                    'status' => 'converted',
                    'opportunity_id' => $opportunity->id,
                ], JSON_THROW_ON_ERROR),
                'metadata' => json_encode(['source' => 'lead_conversion'], JSON_THROW_ON_ERROR),
                'occurred_at' => now(),
            ]);

            return $opportunity;
        });
    }

    private function findOrCreateOrganization(FormSubmission $submission): Organization
    {
        $orgName = $submission->organization_name ?? $submission->contact_name.' (Individual)';

        $duplicates = $this->duplicateDetection->findDuplicateOrganizations($orgName);

        if ($duplicates->isNotEmpty()) {
            $best = $duplicates->first();
            Log::info('CRM: RFQ linked to existing organization via duplicate detection.', [
                'organization_id' => $best->id,
                'form_submission_id' => $submission->id,
            ]);

            return $best;
        }

        return Organization::query()->create([
            'name' => $orgName,
            'type' => 'prospect',
            'email' => $submission->email,
            'phone' => $submission->phone,
            'duplicate_status' => 'none',
        ]);
    }

    private function findOrCreateContact(FormSubmission $submission, Organization $organization): Contact
    {
        if ($submission->email) {
            $existing = Contact::query()
                ->where('email', Str::lower($submission->email))
                ->where('organization_id', $organization->id)
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        $nameParts = explode(' ', trim($submission->contact_name), 2);

        return Contact::query()->create([
            'organization_id' => $organization->id,
            'first_name' => $nameParts[0],
            'last_name' => $nameParts[1] ?? '',
            'email' => $submission->email ? Str::lower($submission->email) : null,
            'phone' => $submission->phone,
            'locale' => $submission->locale ?? 'en',
            'is_primary' => true,
            'role_type' => 'stakeholder',
            'duplicate_status' => 'none',
        ]);
    }
}
