<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contacts · Commercial Workspace · IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">Commercial Workspace</p>
            <h1>Stakeholder Contacts</h1>
        </div>
    </header>

    @if (session('status'))
        <p role="status" style="color: green; font-weight: bold;">{{ session('status') }}</p>
    @endif

    <section style="border: 1px solid #ccc; padding: 1.5rem; border-radius: 8px; margin-top: 1rem;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #ccc; text-align: left;">
                    <th style="padding: 0.5rem;">Contact Name</th>
                    <th style="padding: 0.5rem;">Organization</th>
                    <th style="padding: 0.5rem;">Role Type</th>
                    <th style="padding: 0.5rem;">Email</th>
                    <th style="padding: 0.5rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($contacts as $contact)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 0.5rem;"><strong>{{ $contact->full_name }}</strong></td>
                        <td style="padding: 0.5rem;">{{ $contact->organization?->name }}</td>
                        <td style="padding: 0.5rem;">{{ ucfirst(str_replace('_', ' ', $contact->role_type)) }}</td>
                        <td style="padding: 0.5rem;">{{ $contact->email ?? '-' }}</td>
                        <td style="padding: 0.5rem;"><a href="{{ route('admin.contacts.show', $contact) }}">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="padding: 1rem; text-align: center;">No contacts found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 1rem;">{{ $contacts->links() }}</div>
    </section>
</main>
</body>
</html>
