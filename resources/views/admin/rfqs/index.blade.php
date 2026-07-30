<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RFQ inbox | IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">Commercial workspace</p>
            <h1>RFQ inbox</h1>
            <p>Review, assign and progress genuine customer requests.</p>
        </div>
        <a href="{{ route('admin.content-pages.index') }}">Content management</a>
    </header>

    <div class="admin-table-wrap">
        <table>
            <thead>
            <tr>
                <th>Reference</th>
                <th>Customer</th>
                <th>Service</th>
                <th>Urgency</th>
                <th>Status</th>
                <th>Owner</th>
                <th>Submitted</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($rfqs as $rfq)
                <tr>
                    <td><a href="{{ route('admin.rfqs.show', $rfq) }}">{{ $rfq->reference ?? $rfq->id }}</a></td>
                    <td>
                        <strong>{{ $rfq->organization_name ?: $rfq->contact_name }}</strong><br>
                        <small>{{ $rfq->email }}</small>
                    </td>
                    <td>{{ $rfq->service_code ?: 'Not specified' }}</td>
                    <td>{{ $rfq->urgency ?: 'Standard' }}</td>
                    <td>{{ str_replace('_', ' ', ucfirst($rfq->status)) }}</td>
                    <td>{{ $rfq->assignee?->name ?? 'Unassigned' }}</td>
                    <td>{{ $rfq->submitted_at?->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="7">No RFQs have been received.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $rfqs->links() }}
</main>
</body>
</html>
