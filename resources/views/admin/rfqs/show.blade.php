<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $rfq->reference ?? 'RFQ' }} | IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">RFQ detail</p>
            <h1>{{ $rfq->reference ?? $rfq->id }}</h1>
            <p>{{ $rfq->organization_name ?: $rfq->contact_name }}</p>
        </div>
        <a href="{{ route('admin.rfqs.index') }}">Back to RFQ inbox</a>
    </header>

    @if (session('status'))
        <p role="status">{{ session('status') }}</p>
    @endif

    <section class="admin-detail-grid">
        <article>
            <h2>Customer request</h2>
            <dl>
                <dt>Contact</dt><dd>{{ $rfq->contact_name }}</dd>
                <dt>Organization</dt><dd>{{ $rfq->organization_name ?: 'Not provided' }}</dd>
                <dt>Email</dt><dd><a href="mailto:{{ $rfq->email }}">{{ $rfq->email }}</a></dd>
                <dt>Phone</dt><dd>{{ $rfq->phone ?: 'Not provided' }}</dd>
                <dt>Service</dt><dd>{{ $rfq->service_code ?: 'Not specified' }}</dd>
                <dt>Property type</dt><dd>{{ $rfq->property_type ?: 'Not specified' }}</dd>
                <dt>Estimated quantity</dt><dd>{{ $rfq->estimated_quantity ?: 'Not specified' }}</dd>
                <dt>Urgency</dt><dd>{{ $rfq->urgency ?: 'Standard' }}</dd>
                <dt>Submitted</dt><dd>{{ $rfq->submitted_at?->format('d M Y H:i') }}</dd>
                <dt>Message</dt><dd>{{ $rfq->message ?: 'No additional message.' }}</dd>
            </dl>

            <h2>Attachments</h2>
            @forelse ($rfq->attachments as $attachment)
                <p>
                    <a href="{{ route('admin.rfqs.attachments.download', [$rfq, $attachment]) }}">
                        {{ $attachment->original_name }}
                    </a>
                    <small>
                        {{ number_format($attachment->size_bytes / 1024, 1) }} KB · {{ $attachment->mime_type }}
                    </small>
                </p>
            @empty
                <p>No attachments were submitted.</p>
            @endforelse
        </article>

        <aside>
            <h2>Workflow</h2>
            <form method="post" action="{{ route('admin.rfqs.update', $rfq) }}">
                @csrf
                @method('PUT')

                <label>Status
                    <select name="status" required>
                        @foreach (['new', 'qualified', 'in_progress', 'awaiting_client', 'closed_won', 'closed_lost'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $rfq->status) === $status)>
                                {{ str_replace('_', ' ', ucfirst($status)) }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label>Assigned owner
                    <select name="assigned_to">
                        <option value="">Unassigned</option>
                        @foreach ($assignees as $assignee)
                            <option value="{{ $assignee->id }}" @selected((string) old('assigned_to', $rfq->assigned_to) === (string) $assignee->id)>
                                {{ $assignee->name }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <input type="checkbox" name="mark_contacted" value="1">
                    Mark customer as contacted now
                </label>

                @if ($rfq->last_contacted_at)
                    <p>Last contacted: {{ $rfq->last_contacted_at->format('d M Y H:i') }}</p>
                @endif

                <button type="submit">Update workflow</button>
            </form>
        </aside>
    </section>
</main>
</body>
</html>
