<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $contact->full_name }} · Commercial Workspace · IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">Commercial Workspace / Contacts</p>
            <h1>{{ $contact->full_name }}</h1>
        </div>
        <div><a href="{{ route('admin.contacts.index') }}">← Back to Contacts</a></div>
    </header>

    <section style="border: 1px solid #ccc; padding: 1.5rem; border-radius: 8px; margin-top: 1rem;">
        <h2>Contact Details</h2>
        <p><strong>Organization:</strong> {{ $contact->organization?->name }}</p>
        <p><strong>Email:</strong> {{ $contact->email ?? 'N/A' }}</p>
        <p><strong>Role Type:</strong> {{ ucfirst(str_replace('_', ' ', $contact->role_type)) }}</p>
    </section>
</main>
</body>
</html>
