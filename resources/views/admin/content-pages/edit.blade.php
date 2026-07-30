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
        <a href="{{ route('admin.content-pages.index') }}">Back to pages</a>
    </header>

    @if (session('status'))
        <p role="status">{{ session('status') }}</p>
    @endif

    <form method="post" action="{{ route('admin.content-pages.update', $contentPage) }}">
        @method('put')
        @include('admin.content-pages._form')
    </form>

    <form method="post" action="{{ route('admin.content-pages.destroy', $contentPage) }}" onsubmit="return confirm('Archive this content page?')">
        @csrf
        @method('delete')
        <button type="submit">Archive page</button>
    </form>
</main>
</body>
</html>
