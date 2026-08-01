<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit content page · IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">Content & Growth Console</p>
            <h1>Edit content page</h1>
        </div>
        <div>
            <a href="{{ route('admin.content-pages.preview', $contentPage) }}" target="_blank">Preview (Noindex)</a>
            <a href="{{ route('admin.content-pages.index') }}">Back to pages</a>
        </div>
    </header>

    @if (session('status'))
        <p role="status">{{ session('status') }}</p>
    @endif

    <div class="admin-status-bar">
        <span>Bilingual Approval:</span>
        <span class="badge {{ $contentPage->isTranslationApproved('en') ? 'badge-success' : 'badge-warning' }}">
            EN: {{ $contentPage->isTranslationApproved('en') ? 'Approved' : 'Pending' }}
        </span>
        <span class="badge {{ $contentPage->isTranslationApproved('ar') ? 'badge-success' : 'badge-warning' }}">
            AR: {{ $contentPage->isTranslationApproved('ar') ? 'Approved' : 'Pending' }}
        </span>
    </div>

    <div class="admin-actions-bar">
        @if ($contentPage->isBilinguallyApproved() && $contentPage->status !== 'approved')
            <form method="post" action="{{ route('admin.content-pages.approve', $contentPage) }}">
                @csrf
                <button type="submit">Approve Page</button>
            </form>
        @endif

        @if ($contentPage->isBilinguallyApproved() && $contentPage->status !== 'published')
            <form method="post" action="{{ route('admin.content-pages.publish', $contentPage) }}">
                @csrf
                <button type="submit">Publish Page</button>
            </form>
        @endif

        @if ($contentPage->status === 'published')
            <form method="post" action="{{ route('admin.content-pages.unpublish', $contentPage) }}">
                @csrf
                <button type="submit">Unpublish Page</button>
            </form>
        @endif
    </div>

    <form method="post" action="{{ route('admin.content-pages.update', $contentPage) }}">
        @method('put')
        @include('admin.content-pages._form')
    </form>

    <section class="admin-revisions-section">
        <h2>Revision History</h2>
        <div class="admin-table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Revision #</th>
                    <th>Status</th>
                    <th>Summary</th>
                    <th>Created At</th>
                    <th>Author</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($contentPage->revisions as $revision)
                    <tr>
                        <td>#{{ $revision->revision_number }}</td>
                        <td>{{ ucfirst($revision->status) }}</td>
                        <td>{{ $revision->change_summary }}</td>
                        <td>{{ $revision->created_at?->toIso8601String() }}</td>
                        <td>{{ $revision->author?->name ?? 'System' }}</td>
                        <td>
                            <form method="post" action="{{ route('admin.content-pages.revisions.restore', [$contentPage, $revision]) }}">
                                @csrf
                                <button type="submit">Restore as New Draft</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">No revisions recorded yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <form method="post" action="{{ route('admin.content-pages.destroy', $contentPage) }}" onsubmit="return confirm('Archive this content page?')">
        @csrf
        @method('delete')
        <button type="submit">Archive page</button>
    </form>
</main>
</body>
</html>
