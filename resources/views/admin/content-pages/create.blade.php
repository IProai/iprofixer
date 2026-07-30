<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create content page · IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">Content & Growth Console</p>
            <h1>Create content page</h1>
        </div>
        <a href="{{ route('admin.content-pages.index') }}">Back to pages</a>
    </header>

    <form method="post" action="{{ route('admin.content-pages.store') }}">
        @include('admin.content-pages._form')
    </form>
</main>
</body>
</html>
