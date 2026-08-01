<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

final class RfqReportController extends Controller
{
    private const OPEN_STATUSES = [
        'new',
        'qualified',
        'in_progress',
        'awaiting_client',
    ];

    public function __invoke(Request $request): View
    {
        abort_unless($request->user()?->can('rfq.manage'), 403);

        $firstResponseHours = (int) config('iprofixer.rfq_sla.first_response_hours', 4);
        $staleContactHours = (int) config('iprofixer.rfq_sla.stale_contact_hours', 48);
        $now = now();

        $baseQuery = FormSubmission::query()->where('type', 'rfq');
        $total = (clone $baseQuery)->count();
        $open = (clone $baseQuery)->whereIn('status', self::OPEN_STATUSES)->count();
        $unassigned = (clone $baseQuery)
            ->whereIn('status', self::OPEN_STATUSES)
            ->whereNull('assigned_to')
            ->count();
        $untouched = (clone $baseQuery)
            ->whereIn('status', self::OPEN_STATUSES)
            ->whereNull('last_contacted_at')
            ->count();
        $won = (clone $baseQuery)->where('status', 'closed_won')->count();
        $lost = (clone $baseQuery)->where('status', 'closed_lost')->count();

        $firstResponseCutoff = $now->copy()->subHours($firstResponseHours);
        $staleContactCutoff = $now->copy()->subHours($staleContactHours);

        $breached = (clone $baseQuery)
            ->whereIn('status', self::OPEN_STATUSES)
            ->whereNull('last_contacted_at')
            ->where('submitted_at', '<=', $firstResponseCutoff)
            ->count();

        $stale = (clone $baseQuery)
            ->whereIn('status', self::OPEN_STATUSES)
            ->whereNotNull('last_contacted_at')
            ->where('last_contacted_at', '<=', $staleContactCutoff)
            ->count();

        $statusCounts = (clone $baseQuery)
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $ownerWorkload = User::query()
            ->where('is_active', true)
            ->withCount([
                'assignedRfqs as open_rfq_count' => fn ($query) => $query
                    ->where('type', 'rfq')
                    ->whereIn('status', self::OPEN_STATUSES),
            ])
            ->orderByDesc('open_rfq_count')
            ->orderBy('name')
            ->get(['id', 'name']);

        $atRisk = (clone $baseQuery)
            ->whereIn('status', self::OPEN_STATUSES)
            ->where(function ($query) use ($firstResponseCutoff, $staleContactCutoff): void {
                $query
                    ->where(function ($untouchedQuery) use ($firstResponseCutoff): void {
                        $untouchedQuery
                            ->whereNull('last_contacted_at')
                            ->where('submitted_at', '<=', $firstResponseCutoff);
                    })
                    ->orWhere(function ($staleQuery) use ($staleContactCutoff): void {
                        $staleQuery
                            ->whereNotNull('last_contacted_at')
                            ->where('last_contacted_at', '<=', $staleContactCutoff);
                    });
            })
            ->with('assignee')
            ->oldest('submitted_at')
            ->limit(25)
            ->get();

        $conversionBase = $won + $lost;
        $conversionRate = $conversionBase > 0
            ? round(($won / $conversionBase) * 100, 1)
            : null;

        return view('admin.rfqs.report', [
            'metrics' => [
                'total' => $total,
                'open' => $open,
                'unassigned' => $unassigned,
                'untouched' => $untouched,
                'breached' => $breached,
                'stale' => $stale,
                'won' => $won,
                'lost' => $lost,
                'conversion_rate' => $conversionRate,
            ],
            'statusCounts' => $statusCounts,
            'ownerWorkload' => $ownerWorkload,
            'atRisk' => $atRisk,
            'firstResponseHours' => $firstResponseHours,
            'staleContactHours' => $staleContactHours,
        ]);
    }
}
