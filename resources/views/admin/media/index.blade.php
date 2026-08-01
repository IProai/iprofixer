<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Media Library · IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">Content & Growth Console</p>
            <h1>Media Library</h1>
        </div>
        <a href="{{ route('admin.media.create') }}">Upload new asset</a>
    </header>

    @if (session('status'))
        <p role="status">{{ session('status') }}</p>
    @endif

    <div class="admin-filter-bar">
        <form method="get" action="{{ route('admin.media.index') }}">
            <input type="search" name="search" placeholder="Search filename or alt text..." value="{{ request('search') }}">
            <select name="status">
                <option value="">All Statuses</option>
                <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending Approval</option>
            </select>
            <label>
                <input type="checkbox" name="trashed" value="1" @checked(request()->boolean('trashed'))> Show Archived
            </label>
            <button type="submit">Filter</button>
        </form>
    </div>

    <div class="admin-table-wrap">
        <table>
            <thead>
            <tr>
                <th>Preview</th>
                <th>Original Name</th>
                <th>MIME / Size</th>
                <th>Dimensions</th>
                <th>Bilingual Alt Text</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($assets as $asset)
                <tr>
                    <td>
                        @if ($asset->isImage())
                            <img src="{{ $asset->getUrl() }}" alt="{{ $asset->alt_text_en ?? 'Thumbnail' }}" width="60" height="40" style="object-fit: cover; border-radius: 4px;">
                        @else
                            <span>📄 {{ strtoupper($asset->extension ?? 'FILE') }}</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $asset->original_name }}</strong>
                        @if ($asset->is_decorative)
                            <span class="badge">Decorative</span>
                        @endif
                    </td>
                    <td>{{ $asset->mime_type }} ({{ round($asset->size_bytes / 1024, 1) }} KB)</td>
                    <td>{{ $asset->width ? "{$asset->width}×{$asset->height}px" : 'N/A' }}</td>
                    <td>
                        @if ($asset->is_decorative)
                            <em>None (Decorative)</em>
                        @else
                            EN: {{ $asset->alt_text_en ? '✓' : '✗' }} | AR: {{ $asset->alt_text_ar ? '✓' : '✗' }}
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $asset->usage_status === 'approved' ? 'badge-success' : 'badge-warning' }}">
                            {{ ucfirst($asset->usage_status) }}
                        </span>
                    </td>
                    <td>
                        @if ($asset->trashed())
                            <form method="post" action="{{ route('admin.media.restore', $asset->id) }}" style="display:inline;">
                                @csrf
                                <button type="submit">Restore</button>
                            </form>
                        @else
                            <a href="{{ route('admin.media.edit', $asset) }}">Edit</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">No media assets found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $assets->links() }}
</main>
</body>
</html>
