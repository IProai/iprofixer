<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Opportunities · Commercial Workspace · IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">Commercial Workspace</p>
            <h1>Sales Pipeline & Opportunities</h1>
        </div>
    </header>

    @if (session('status'))
        <p role="status" style="color: green; font-weight: bold;">{{ session('status') }}</p>
    @endif

    <section style="border: 1px solid #ccc; padding: 1.5rem; border-radius: 8px; margin-top: 1rem;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #ccc; text-align: left;">
                    <th style="padding: 0.5rem;">Opportunity Title</th>
                    <th style="padding: 0.5rem;">Organization</th>
                    <th style="padding: 0.5rem;">Stage</th>
                    <th style="padding: 0.5rem;">Value</th>
                    <th style="padding: 0.5rem;">Prob %</th>
                    <th style="padding: 0.5rem;">Next Action</th>
                    <th style="padding: 0.5rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($opportunities as $op)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 0.5rem;"><strong>{{ $op->title }}</strong></td>
                        <td style="padding: 0.5rem;">{{ $op->organization?->name }}</td>
                        <td style="padding: 0.5rem;"><span class="badge badge-stage-{{ $op->stage }}">{{ strtoupper($op->stage) }}</span></td>
                        <td style="padding: 0.5rem;">{{ $op->estimated_value ? number_format((float)$op->estimated_value, 2) . ' ' . $op->currency_code : '-' }}</td>
                        <td style="padding: 0.5rem;">{{ $op->probability }}%</td>
                        <td style="padding: 0.5rem;">{{ $op->next_action ?? '-' }}</td>
                        <td style="padding: 0.5rem;"><a href="{{ route('admin.opportunities.show', $op) }}">View / Manage</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 1rem; text-align: center;">No opportunities found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 1rem;">
            {{ $opportunities->links() }}
        </div>
    </section>
</main>
</body>
</html>
