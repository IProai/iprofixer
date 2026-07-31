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

    <section aria-labelledby="rfq-filter-heading">
        <h2 id="rfq-filter-heading">Find requests</h2>
        <form method="get" action="{{ route('admin.rfqs.index') }}" class="admin-filter-form">
            <label>
                Search
                <input
                    type="search"
                    name="search"
                    value="{{ $filters['search'] }}"
                    placeholder="Reference, customer, company or email"
                >
            </label>

            <label>
                Status
                <select name="status">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected($filters['status'] === $status)>
                            {{ str_replace('_', ' ', ucfirst($status)) }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>
                Owner
                <select name="owner">
                    <option value="">All owners</option>
                    <option value="unassigned" @selected($filters['owner'] === 'unassigned')>Unassigned</option>
                    @foreach ($assignees as $assignee)
                        <option value="{{ $assignee->id }}" @selected($filters['owner'] === (string) $assignee->id)>
                            {{ $assignee->name }}
                        </option>
                    @endforeach
                </select>
            </label>

            <div class="admin-filter-actions">
                <button type="submit">Apply filters</button>
                @if ($filters['search'] !== '' || $filters['status'] !== '' || $filters['owner'] !== '')
                    <a href="{{ route('admin.rfqs.index') }}">Clear filters</a>
                @endif
            </div>
        </form>
    </section>

    <p role="status">
        {{ number_format($rfqs->total()) }} {{ Str::plural('request', $rfqs->total()) }} found.
    </p>

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
                <tr>
                    <td colspan="7">
                        @if ($filters['search'] !== '' || $filters['status'] !== '' || $filters['owner'] !== '')
                            No RFQs match the current filters.
                        @else
                            No RFQs have been received.
                        @endif
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $rfqs->links() }}
</main>
</body>
</html>
