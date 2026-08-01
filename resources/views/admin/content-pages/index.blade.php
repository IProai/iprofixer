<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Content pages · IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">Content & Growth Console</p>
            <h1>Content pages</h1>
        </div>
        <a href="{{ route('admin.content-pages.create') }}">Create page</a>
    </header>

    @if (session('status'))
        <p role="status">{{ session('status') }}</p>
    @endif

    <div class="admin-table-wrap">
        <table>
            <thead>
            <tr>
                <th>English title</th>
                <th>Arabic title</th>
                <th>Type</th>
                <th>Status</th>
                <th>Bilingual Approval</th>
                <th>Updated</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($pages as $page)
                <tr>
                    <td>{{ $page->title_en }}</td>
                    <td dir="rtl">{{ $page->title_ar }}</td>
                    <td>{{ ucfirst($page->type) }}</td>
                    <td>{{ ucfirst($page->status) }}</td>
                    <td>
                        EN: {{ $page->isTranslationApproved('en') ? '✓' : '✗' }} |
                        AR: {{ $page->isTranslationApproved('ar') ? '✓' : '✗' }}
                    </td>
                    <td>{{ $page->updated_at?->diffForHumans() }}</td>
                    <td>
                        <a href="{{ route('admin.content-pages.preview', $page) }}" target="_blank">Preview</a> |
                        <a href="{{ route('admin.content-pages.edit', $page) }}">Edit</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">No content pages yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $pages->links() }}
</main>
</body>
</html>
