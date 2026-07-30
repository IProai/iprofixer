<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreRfqRequest;
use App\Models\FormSubmission;
use App\Notifications\NewRfqOperationsNotification;
use App\Notifications\RfqReceivedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

final class RfqSubmissionController
{
    public function __invoke(StoreRfqRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $correlationId = (string) Str::uuid();
        $reference = sprintf('RFQ-%s-%s', now()->format('Ymd'), Str::upper(Str::random(6)));
        $locale = in_array(app()->getLocale(), ['en', 'ar'], true) ? app()->getLocale() : 'en';
        $storedPaths = [];

        try {
            $submission = DB::transaction(function () use ($validated, $request, $correlationId, $reference, $locale, &$storedPaths): FormSubmission {
                $submission = FormSubmission::query()->create([
                    'reference' => $reference,
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

                foreach ($validated['attachments'] ?? [] as $attachment) {
                    if (! $attachment instanceof UploadedFile) {
                        continue;
                    }

                    $extension = Str::lower((string) $attachment->guessExtension());
                    $filename = Str::uuid().($extension !== '' ? ".{$extension}" : '');
                    $path = $attachment->storeAs("rfq/{$submission->getKey()}", $filename, 'local');

                    if ($path === false) {
                        throw new \RuntimeException('RFQ attachment storage failed.');
                    }

                    $storedPaths[] = $path;
                    $realPath = $attachment->getRealPath();

                    $submission->attachments()->create([
                        'disk' => 'local',
                        'path' => $path,
                        'original_name' => Str::limit($attachment->getClientOriginalName(), 255, ''),
                        'mime_type' => (string) $attachment->getMimeType(),
                        'size_bytes' => $attachment->getSize(),
                        'sha256' => $realPath === false ? hash('sha256', $path) : hash_file('sha256', $realPath),
                    ]);
                }

                DB::table('audit_events')->insert([
                    'id' => (string) Str::uuid(),
                    'actor_id' => null,
                    'action' => 'public.rfq.submitted',
                    'subject_type' => FormSubmission::class,
                    'subject_id' => $submission->getKey(),
                    'correlation_id' => $correlationId,
                    'ip_address' => null,
                    'user_agent' => Str::limit((string) $request->userAgent(), 1000),
                    'before' => null,
                    'after' => json_encode(['status' => 'new', 'reference' => $reference], JSON_THROW_ON_ERROR),
                    'metadata' => json_encode([
                        'locale' => $locale,
                        'source_page' => $submission->source_page,
                        'attachment_count' => count($storedPaths),
                    ], JSON_THROW_ON_ERROR),
                    'occurred_at' => now(),
                ]);

                return $submission;
            });
        } catch (Throwable $exception) {
            foreach ($storedPaths as $path) {
                Storage::disk('local')->delete($path);
            }

            throw $exception;
        }

        $this->sendNotifications($submission, $locale);

        return back()->with('rfq_submitted', $reference);
    }

    private function sendNotifications(FormSubmission $submission, string $locale): void
    {
        try {
            Notification::route('mail', $submission->email)->notify(
                new RfqReceivedNotification($submission->reference, $submission->contact_name, $locale),
            );

            $operationsEmail = config('iprofixer.rfq_operations_email');

            if (is_string($operationsEmail) && filter_var($operationsEmail, FILTER_VALIDATE_EMAIL)) {
                Notification::route('mail', $operationsEmail)->notify(
                    new NewRfqOperationsNotification($submission),
                );
            }
        } catch (Throwable $exception) {
            Log::warning('RFQ notification delivery failed.', [
                'reference' => $submission->reference,
                'exception' => $exception::class,
            ]);
        }
    }
}
