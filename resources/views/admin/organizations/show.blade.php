<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $organization->name }} · Commercial Workspace · IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">Commercial Workspace / Organizations</p>
            <h1>{{ $organization->name }}</h1>
        </div>
        <div><a href="{{ route('admin.organizations.index') }}">← Back to Organizations</a></div>
    </header>

    <section style="border: 1px solid #ccc; padding: 1.5rem; border-radius: 8px; margin-top: 1rem;">
        <h2>Organization Summary</h2>
        <p><strong>Type:</strong> {{ ucfirst($organization->type) }}</p>
        <p><strong>Duplicate Governance Status:</strong> {{ ucfirst($organization->duplicate_status) }}</p>
        <p><strong>Properties Count:</strong> {{ $organization->properties->count() }}</p>
        <p><strong>Contacts Count:</strong> {{ $organization->contacts->count() }}</p>
    </section>
</main>
</body>
</html>
