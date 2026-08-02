<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Organizations · Commercial Workspace · IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">Commercial Workspace</p>
            <h1>Organizations & Accounts</h1>
        </div>
    </header>

    @if (session('status'))
        <p role="status" style="color: green; font-weight: bold;">{{ session('status') }}</p>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-top: 1rem;">
        <section style="border: 1px solid #ccc; padding: 1.5rem; border-radius: 8px;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #ccc; text-align: left;">
                        <th style="padding: 0.5rem;">Organization Name</th>
                        <th style="padding: 0.5rem;">Type</th>
                        <th style="padding: 0.5rem;">Group</th>
                        <th style="padding: 0.5rem;">Properties</th>
                        <th style="padding: 0.5rem;">Dup Status</th>
                        <th style="padding: 0.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($organizations as $org)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 0.5rem;"><strong>{{ $org->name }}</strong></td>
                            <td style="padding: 0.5rem;">{{ ucfirst($org->type) }}</td>
                            <td style="padding: 0.5rem;">{{ $org->group?->name ?? '-' }}</td>
                            <td style="padding: 0.5rem;">{{ $org->properties->count() }}</td>
                            <td style="padding: 0.5rem;">
                                @if ($org->hasSuspectedDuplicate())
                                    <span style="color: orange; font-weight: bold;">⚠️ {{ ucfirst($org->duplicate_status) }}</span>
                                @else
                                    <span style="color: green;">Clean</span>
                                @endif
                            </td>
                            <td style="padding: 0.5rem;"><a href="{{ route('admin.organizations.show', $org) }}">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="padding: 1rem; text-align: center;">No organizations found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div style="margin-top: 1rem;">{{ $organizations->links() }}</div>
        </section>

        <section style="border: 1px solid #ccc; padding: 1.5rem; border-radius: 8px;">
            <h2>Add Organization</h2>
            <form method="post" action="{{ route('admin.organizations.store') }}">
                @csrf
                <label style="display: block; margin-bottom: 0.5rem;">
                    Organization Name:
                    <input name="name" required style="width: 100%;">
                </label>
                <label style="display: block; margin-bottom: 0.5rem;">
                    Type:
                    <select name="type" style="width: 100%;">
                        <option value="prospect">Prospect</option>
                        <option value="client">Client</option>
                        <option value="partner">Partner</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </label>
                <button type="submit" style="background: #002b49; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px;">Save Organization</button>
            </form>
        </section>
    </div>
</main>
</body>
</html>
