<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreRfqRequest;
use App\Models\FormSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class RfqSubmissionController
{
    public function __invoke(StoreRfqRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $correlationId = (string) Str::uuid();
        $locale = in_array(app()->getLocale(), ['en', 'ar'], true) ? app()->getLocale() : 'en';

        DB::transaction(function () use ($validated, $request, $correlationId, $locale): void {
            $submission = FormSubmission::query()->create([
                'type' => 'rfq',
                'status' => 'new',
                'locale' => $locale,
                'source_page' => $validated['source_page'] ?? $request->headers->get('referer'),
                'campaign_source' => $validated['campaign_source'] ?? null,
                'campaign_medium' => $validated['campaign_medium'] ?? null,
                'campaign_name' => $validated['campaign_name'] ?? null,
                'contact_name' => $validated['contact_name'],
                'organization_name' => $validated['organization_name'] ?? null,
                'email' => Str::lower($validated['email']),
                'phone' => $validated['phone'] ?? null,
                'service_code' => $validated['service_code'] ?? null,
                'property_type' => $validated['property_type'] ?? null,
                'urgency' => $validated['urgency'] ?? null,
                'estimated_quantity' => $validated['estimated_quantity'] ?? null,
                'message' => $validated['message'] ?? null,
                'payload' => ['user_agent' => $request->userAgent()],
                'correlation_id' => $correlationId,
                'ip_hash' => $request->ip() ? hash_hmac('sha256', $request->ip(), (string) config('app.key')) : null,
                'submitted_at' => now(),
            ]);

            $submission->consents()->create([
                'purpose' => 'rfq_follow_up',
                'policy_version' => '2026-07-29',
                'granted' => true,
                'locale' => $locale,
                'recorded_at' => now(),
            ]);

            DB::table('audit_events')->insert([
                'id' => (string) Str::uuid(),
                'actor_id' => null,
                'action' => 'public.rfq.submitted',
                'subject_type' => FormSubmission::class,
                'subject_id' => $submission->getKey(),
                'correlation_id' => $correlationId,
                'ip_address' => null,
                'user_agent' => $request->userAgent(),
                'before' => null,
                'after' => json_encode(['status' => 'new'], JSON_THROW_ON_ERROR),
                'metadata' => json_encode(['locale' => $locale, 'source_page' => $submission->source_page], JSON_THROW_ON_ERROR),
                'occurred_at' => now(),
            ]);
        });

        return back()->with('rfq_submitted', $correlationId);
    }
}
