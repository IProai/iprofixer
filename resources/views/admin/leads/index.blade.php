<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Leads · Commercial Workspace · IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">Commercial Workspace</p>
            <h1>Leads & Inquiries</h1>
        </div>
    </header>

    @if (session('status'))
        <p role="status" style="color: green; font-weight: bold;">{{ session('status') }}</p>
    @endif

    <section style="border: 1px solid #ccc; padding: 1.5rem; border-radius: 8px; margin-top: 1rem;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #ccc; text-align: left;">
                    <th style="padding: 0.5rem;">Organization / Contact</th>
                    <th style="padding: 0.5rem;">Source</th>
                    <th style="padding: 0.5rem;">Status</th>
                    <th style="padding: 0.5rem;">Service</th>
                    <th style="padding: 0.5rem;">Created</th>
                    <th style="padding: 0.5rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($leads as $lead)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 0.5rem;">
                            <strong>{{ $lead->organization?->name ?? 'Unlinked' }}</strong><br>
                            <small>{{ $lead->contact?->full_name }}</small>
                        </td>
                        <td style="padding: 0.5rem;">{{ strtoupper($lead->source) }}</td>
                        <td style="padding: 0.5rem;">
                            <span class="badge badge-{{ $lead->status }}">{{ ucfirst($lead->status) }}</span>
                        </td>
                        <td style="padding: 0.5rem;">{{ $lead->service_code ?? '-' }}</td>
                        <td style="padding: 0.5rem;">{{ $lead->created_at->format('Y-m-d H:i') }}</td>
                        <td style="padding: 0.5rem;">
                            <a href="{{ route('admin.leads.show', $lead) }}">View / Qualify</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 1rem; text-align: center;">No leads found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 1rem;">
            {{ $leads->links() }}
        </div>
    </section>
</main>
</body>
</html>
