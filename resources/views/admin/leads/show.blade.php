<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lead Details · Commercial Workspace · IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">Commercial Workspace / Leads</p>
            <h1>Lead: {{ $lead->organization?->name ?? $lead->contact?->full_name }}</h1>
        </div>
        <div>
            <a href="{{ route('admin.leads.index') }}">← Back to Leads</a>
        </div>
    </header>

    @if (session('status'))
        <p role="status" style="color: green; font-weight: bold;">{{ session('status') }}</p>
    @endif
    @if (session('error'))
        <p role="alert" style="color: red; font-weight: bold;">{{ session('error') }}</p>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-top: 1rem;">
        <div>
            <section style="border: 1px solid #ccc; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <h2>Lead Details</h2>
                <dl style="display: grid; grid-template-columns: 1fr 2fr; gap: 0.5rem;">
                    <dt>Status:</dt> <dd><span class="badge badge-{{ $lead->status }}">{{ ucfirst($lead->status) }}</span></dd>
                    <dt>Source:</dt> <dd>{{ strtoupper($lead->source) }} ({{ $lead->source_detail ?? 'Direct' }})</dd>
                    <dt>Organization:</dt> <dd>{{ $lead->organization?->name ?? 'None' }}</dd>
                    <dt>Contact:</dt> <dd>{{ $lead->contact?->full_name }} ({{ $lead->contact?->email }})</dd>
                    <dt>Service Code:</dt> <dd>{{ $lead->service_code ?? 'Not specified' }}</dd>
                    <dt>Property Type:</dt> <dd>{{ $lead->property_type ?? 'Not specified' }}</dd>
                    <dt>Urgency:</dt> <dd>{{ $lead->urgency ?? 'Normal' }}</dd>
                    <dt>Est. Quantity:</dt> <dd>{{ $lead->estimated_quantity ?? 'N/A' }}</dd>
                </dl>
            </section>

            @if ($lead->formSubmission)
                <section style="border: 1px solid #ccc; padding: 1.5rem; border-radius: 8px;">
                    <h2>Original RFQ Submission</h2>
                    <p><strong>Message:</strong> {{ $lead->formSubmission->message ?? 'No message.' }}</p>
                    <p><strong>Submitted At:</strong> {{ $lead->formSubmission->submitted_at?->format('Y-m-d H:i') }}</p>
                </section>
            @endif
        </div>

        <div>
            <section style="border: 1px solid #ccc; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <h2>Actions</h2>

                @if ($lead->isNew())
                    <form method="post" action="{{ route('admin.leads.qualify', $lead) }}" style="margin-bottom: 1rem;">
                        @csrf
                        <h3>Qualify Lead</h3>
                        <textarea name="qualification_notes" placeholder="Qualification notes..." style="width: 100%; margin-bottom: 0.5rem;"></textarea>
                        <button type="submit" style="background: green; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px;">Qualify</button>
                    </form>

                    <form method="post" action="{{ route('admin.leads.disqualify', $lead) }}">
                        @csrf
                        <h3>Disqualify Lead</h3>
                        <input name="disqualification_reason" placeholder="Reason..." required style="width: 100%; margin-bottom: 0.5rem;">
                        <button type="submit" style="background: red; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px;">Disqualify</button>
                    </form>
                @elseif ($lead->isQualified())
                    <form method="post" action="{{ route('admin.leads.convert', $lead) }}">
                        @csrf
                        <h3>Convert to Opportunity</h3>
                        <label style="display: block; margin-bottom: 0.5rem;">
                            Opportunity Title:
                            <input name="title" value="{{ $lead->organization?->name }} — Care Engagement" required style="width: 100%;">
                        </label>
                        <label style="display: block; margin-bottom: 0.5rem;">
                            Est. Value (AED):
                            <input name="estimated_value" type="number" step="0.01" style="width: 100%;">
                        </label>
                        <button type="submit" style="background: #d4af37; color: #000; padding: 0.5rem 1rem; font-weight: bold; border: none; border-radius: 4px;">Convert to Opportunity</button>
                    </form>
                @elseif ($lead->isConverted())
                    <p style="color: green;">✔ Converted to Opportunity.</p>
                    @if ($lead->opportunity)
                        <a href="{{ route('admin.opportunities.show', $lead->opportunity) }}">View Opportunity →</a>
                    @endif
                @elseif ($lead->isDisqualified())
                    <p style="color: red;">✖ Disqualified: {{ $lead->disqualification_reason }}</p>
                @endif
            </section>
        </div>
    </div>
</main>
</body>
</html>
