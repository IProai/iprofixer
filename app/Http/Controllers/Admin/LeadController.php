<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\LeadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class LeadController extends Controller
{
    public function __construct(private readonly LeadService $leadService) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()?->can('lead.view'), 403);

        $leads = Lead::query()
            ->with(['organization', 'contact', 'assignee'])
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->input('source'), fn ($q, $s) => $q->where('source', $s))
            ->latest()
            ->paginate(25);

        return view('admin.leads.index', compact('leads'));
    }

    public function show(Request $request, Lead $lead): View
    {
        abort_unless($request->user()?->can('lead.view'), 403);

        $lead->load(['formSubmission', 'organization', 'contact', 'property', 'assignee', 'opportunity']);

        return view('admin.leads.show', compact('lead'));
    }

    public function qualify(Request $request, Lead $lead): RedirectResponse
    {
        abort_unless($request->user()?->can('lead.qualify'), 403);

        if ($lead->isConverted() || $lead->isDisqualified()) {
            return back()->with('error', 'Lead cannot be qualified in its current state.');
        }

        $validated = $request->validate([
            'qualification_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $lead->update([
            'status' => 'qualified',
            'qualified_at' => now(),
            'qualification_notes' => $validated['qualification_notes'] ?? $lead->qualification_notes,
            'updated_by' => $request->user()->getKey(),
        ]);

        DB::table('audit_events')->insert([
            'id' => (string) Str::uuid(),
            'actor_id' => $request->user()->getKey(),
            'action' => 'crm.lead.qualified',
            'subject_type' => Lead::class,
            'subject_id' => (string) $lead->id,
            'correlation_id' => (string) Str::uuid(),
            'before' => json_encode(['status' => 'new'], JSON_THROW_ON_ERROR),
            'after' => json_encode(['status' => 'qualified'], JSON_THROW_ON_ERROR),
            'metadata' => null,
            'occurred_at' => now(),
        ]);

        return back()->with('status', 'Lead qualified.');
    }

    public function disqualify(Request $request, Lead $lead): RedirectResponse
    {
        abort_unless($request->user()?->can('lead.qualify'), 403);

        if ($lead->isConverted()) {
            return back()->with('error', 'A converted lead cannot be disqualified.');
        }

        $validated = $request->validate([
            'disqualification_reason' => ['required', 'string', 'max:255'],
        ]);

        $before = $lead->status;
        $lead->update([
            'status' => 'disqualified',
            'disqualified_at' => now(),
            'disqualification_reason' => $validated['disqualification_reason'],
            'updated_by' => $request->user()->getKey(),
        ]);

        DB::table('audit_events')->insert([
            'id' => (string) Str::uuid(),
            'actor_id' => $request->user()->getKey(),
            'action' => 'crm.lead.disqualified',
            'subject_type' => Lead::class,
            'subject_id' => (string) $lead->id,
            'correlation_id' => (string) Str::uuid(),
            'before' => json_encode(['status' => $before], JSON_THROW_ON_ERROR),
            'after' => json_encode([
                'status' => 'disqualified',
                'reason' => $validated['disqualification_reason'],
            ], JSON_THROW_ON_ERROR),
            'metadata' => null,
            'occurred_at' => now(),
        ]);

        return back()->with('status', 'Lead disqualified.');
    }

    public function convert(Request $request, Lead $lead): RedirectResponse
    {
        abort_unless($request->user()?->can('lead.convert'), 403);

        if (! $lead->isQualified()) {
            return back()->with('error', 'Only qualified leads may be converted to opportunities.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'service_code' => ['nullable', 'string', 'max:80'],
            'estimated_value' => ['nullable', 'numeric', 'min:0'],
            'currency_code' => ['nullable', 'string', 'size:3'],
            'expected_close_date' => ['nullable', 'date', 'after:today'],
            'next_action' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $opportunity = $this->leadService->convertToOpportunity(
            $lead,
            array_merge($validated, ['assigned_to' => $request->user()->getKey()]),
            $request->user()->getKey(),
        );

        return redirect()
            ->route('admin.opportunities.show', $opportunity)
            ->with('status', 'Lead converted to opportunity.');
    }
}
