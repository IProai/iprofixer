<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RFQ operations report | IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">Commercial workspace</p>
            <h1>RFQ operations report</h1>
            <p>Deterministic workload, response and closure visibility for active enquiries.</p>
        </div>
        <a href="{{ route('admin.rfqs.index') }}">Back to RFQ inbox</a>
    </header>

    <section aria-labelledby="rfq-summary-heading">
        <h2 id="rfq-summary-heading">Operational summary</h2>
        <div class="admin-metric-grid">
            <article><span>Total RFQs</span><strong>{{ number_format($metrics['total']) }}</strong></article>
            <article><span>Open pipeline</span><strong>{{ number_format($metrics['open']) }}</strong></article>
            <article><span>Unassigned</span><strong>{{ number_format($metrics['unassigned']) }}</strong></article>
            <article><span>Untouched</span><strong>{{ number_format($metrics['untouched']) }}</strong></article>
            <article><span>First-response breaches</span><strong>{{ number_format($metrics['breached']) }}</strong></article>
            <article><span>Stale follow-ups</span><strong>{{ number_format($metrics['stale']) }}</strong></article>
            <article><span>Closed won</span><strong>{{ number_format($metrics['won']) }}</strong></article>
            <article>
                <span>Closed conversion</span>
                <strong>{{ $metrics['conversion_rate'] === null ? 'Not available' : $metrics['conversion_rate'].'%' }}</strong>
            </article>
        </div>
        <p class="admin-help-text">
            First-response breach: no contact within {{ $firstResponseHours }} hours. Stale follow-up: last contact older than {{ $staleContactHours }} hours while still open.
        </p>
    </section>

    <section aria-labelledby="rfq-status-heading">
        <h2 id="rfq-status-heading">Pipeline by status</h2>
        <div class="admin-table-wrap">
            <table>
                <thead><tr><th>Status</th><th>Requests</th></tr></thead>
                <tbody>
                @forelse ($statusCounts as $status => $count)
                    <tr>
                        <td>{{ str_replace('_', ' ', ucfirst($status)) }}</td>
                        <td>{{ number_format((int) $count) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2">No RFQ data is available.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section aria-labelledby="rfq-owner-heading">
        <h2 id="rfq-owner-heading">Owner workload</h2>
        <div class="admin-table-wrap">
            <table>
                <thead><tr><th>Owner</th><th>Open RFQs</th></tr></thead>
                <tbody>
                @forelse ($ownerWorkload as $owner)
                    <tr><td>{{ $owner->name }}</td><td>{{ number_format($owner->open_rfq_count) }}</td></tr>
                @empty
                    <tr><td colspan="2">No active operators are available.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section aria-labelledby="rfq-risk-heading">
        <h2 id="rfq-risk-heading">RFQs requiring attention</h2>
        <div class="admin-table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Reference</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Owner</th>
                    <th>Submitted</th>
                    <th>Last contact</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($atRisk as $rfq)
                    <tr>
                        <td><a href="{{ route('admin.rfqs.show', $rfq) }}">{{ $rfq->reference ?? $rfq->id }}</a></td>
                        <td>{{ $rfq->organization_name ?: $rfq->contact_name }}</td>
                        <td>{{ str_replace('_', ' ', ucfirst($rfq->status)) }}</td>
                        <td>{{ $rfq->assignee?->name ?? 'Unassigned' }}</td>
                        <td>{{ $rfq->submitted_at?->format('d M Y H:i') }}</td>
                        <td>{{ $rfq->last_contacted_at?->format('d M Y H:i') ?? 'Never' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6">No open RFQs currently breach the defined response or follow-up windows.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>
