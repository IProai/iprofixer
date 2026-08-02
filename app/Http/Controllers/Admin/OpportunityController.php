<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opportunity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class OpportunityController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->can('opportunity.view'), 403);

        $opportunities = Opportunity::query()
            ->with(['organization', 'property', 'contact', 'assignee'])
            ->when($request->input('stage'), fn ($q, $stage) => $q->where('stage', $stage))
            ->when($request->input('assigned_to'), fn ($q, $userId) => $q->where('assigned_to', $userId))
            ->latest()
            ->paginate(25);

        return view('admin.opportunities.index', compact('opportunities'));
    }

    public function show(Request $request, Opportunity $opportunity): View
    {
        abort_unless($request->user()?->can('opportunity.view'), 403);

        $opportunity->load(['lead', 'organization', 'property', 'contact', 'assignee']);

        return view('admin.opportunities.show', compact('opportunity'));
    }

    public function updateStage(Request $request, Opportunity $opportunity): RedirectResponse
    {
        abort_unless($request->user()?->can('opportunity.edit'), 403);

        $validated = $request->validate([
            'stage' => ['required', 'string', 'in:discovery,assessment,proposal,negotiation,won,lost'],
            'probability' => ['nullable', 'integer', 'between:0,100'],
            'next_action' => ['nullable', 'string', 'max:255'],
            'next_action_due_at' => ['nullable', 'date'],
            'loss_reason' => ['required_if:stage,lost', 'nullable', 'string', 'in:price,competitor,budget_frozen,no_decision,lost_contact,scope_mismatch,other'],
            'loss_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $beforeStage = $opportunity->stage;
        $newStage = $validated['stage'];

        DB::transaction(function () use ($opportunity, $validated, $beforeStage, $newStage, $request): void {
            $updateData = [
                'stage' => $newStage,
                'probability' => $validated['probability'] ?? match ($newStage) {
                    'discovery' => 20,
                    'assessment' => 40,
                    'proposal' => 60,
                    'negotiation' => 80,
                    'won' => 100,
                    'lost' => 0,
                    default => $opportunity->probability,
                },
                'next_action' => $validated['next_action'] ?? $opportunity->next_action,
                'updated_by' => $request->user()->getKey(),
            ];

            if ($newStage === 'won') {
                $updateData['won_at'] = now();
            } elseif ($newStage === 'lost') {
                $updateData['lost_at'] = now();
                $updateData['loss_reason'] = $validated['loss_reason'] ?? null;
                $updateData['loss_notes'] = $validated['loss_notes'] ?? null;
            }

            $opportunity->update($updateData);

            DB::table('audit_events')->insert([
                'id' => (string) Str::uuid(),
                'actor_id' => $request->user()->getKey(),
                'action' => 'crm.opportunity.stage_changed',
                'subject_type' => Opportunity::class,
                'subject_id' => (string) $opportunity->id,
                'correlation_id' => (string) Str::uuid(),
                'before' => json_encode(['stage' => $beforeStage], JSON_THROW_ON_ERROR),
                'after' => json_encode(['stage' => $newStage], JSON_THROW_ON_ERROR),
                'metadata' => null,
                'occurred_at' => now(),
            ]);
        });

        return back()->with('status', 'Opportunity stage updated.');
    }
}
