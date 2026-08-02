<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Opportunity Details · Commercial Workspace · IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">Commercial Workspace / Opportunities</p>
            <h1>{{ $opportunity->title }}</h1>
        </div>
        <div>
            <a href="{{ route('admin.opportunities.index') }}">← Back to Opportunities</a>
        </div>
    </header>

    @if (session('status'))
        <p role="status" style="color: green; font-weight: bold;">{{ session('status') }}</p>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-top: 1rem;">
        <div>
            <section style="border: 1px solid #ccc; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <h2>Opportunity Overview</h2>
                <dl style="display: grid; grid-template-columns: 1fr 2fr; gap: 0.5rem;">
                    <dt>Organization:</dt> <dd>{{ $opportunity->organization?->name }}</dd>
                    <dt>Property:</dt> <dd>{{ $opportunity->property?->name ?? 'All / Unassigned' }}</dd>
                    <dt>Contact:</dt> <dd>{{ $opportunity->contact?->full_name }}</dd>
                    <dt>Current Stage:</dt> <dd><strong>{{ strtoupper($opportunity->stage) }}</strong> ({{ $opportunity->probability }}%)</dd>
                    <dt>Estimated Value:</dt> <dd>{{ $opportunity->estimated_value ? number_format((float)$opportunity->estimated_value, 2) . ' ' . $opportunity->currency_code : 'TBD' }}</dd>
                    <dt>Next Action:</dt> <dd>{{ $opportunity->next_action ?? 'None specified' }}</dd>
                    @if ($opportunity->isLost())
                        <dt>Loss Reason:</dt> <dd style="color: red;">{{ ucfirst($opportunity->loss_reason) }} — {{ $opportunity->loss_notes }}</dd>
                    @endif
                </dl>
            </section>
        </div>

        <div>
            <section style="border: 1px solid #ccc; padding: 1.5rem; border-radius: 8px;">
                <h2>Advance Stage</h2>

                <form method="post" action="{{ route('admin.opportunities.stage', $opportunity) }}">
                    @csrf
                    <label style="display: block; margin-bottom: 0.5rem;">
                        Pipeline Stage:
                        <select name="stage" style="width: 100%;">
                            @foreach (\App\Models\Opportunity::validStages() as $s)
                                <option value="{{ $s }}" @selected($opportunity->stage === $s)>{{ strtoupper($s) }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label style="display: block; margin-bottom: 0.5rem;">
                        Next Action:
                        <input name="next_action" value="{{ $opportunity->next_action }}" style="width: 100%;">
                    </label>

                    <label style="display: block; margin-bottom: 0.5rem;">
                        Loss Reason (if Lost):
                        <select name="loss_reason" style="width: 100%;">
                            <option value="">Select reason if losing...</option>
                            @foreach (\App\Models\Opportunity::lossReasons() as $reason)
                                <option value="{{ $reason }}">{{ ucfirst(str_replace('_', ' ', $reason)) }}</option>
                            @endforeach
                        </select>
                    </label>

                    <button type="submit" style="background: #002b49; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px;">Update Pipeline Stage</button>
                </form>
            </section>
        </div>
    </div>
</main>
</body>
</html>
