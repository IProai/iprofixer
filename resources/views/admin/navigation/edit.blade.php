<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Navigation Item · IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">Content & Growth Console</p>
            <h1>Edit Navigation Item</h1>
        </div>
        <a href="{{ route('admin.navigation.index') }}">Back to Navigation Workspace</a>
    </header>

    @if ($errors->any())
        <div role="alert" class="admin-alert admin-alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ route('admin.navigation.items.update', $item) }}">
        @csrf
        @method('put')

        <div class="admin-form-grid" style="max-width: 600px;">
            <label>Parent Item (Max 1 level)
                <select name="parent_id">
                    <option value="">None (Top Level)</option>
                    @foreach ($availableParents as $parent)
                        <option value="{{ $parent->id }}" @selected(old('parent_id', $item->parent_id) === $parent->id)>{{ $parent->label_en }} / {{ $parent->label_ar }}</option>
                    @endforeach
                </select>
            </label>

            <label>English Label
                <input name="label_en" value="{{ old('label_en', $item->label_en) }}" required>
                @error('label_en')<small>{{ $message }}</small>@enderror
            </label>

            <label dir="rtl">الاسم بالعربية
                <input name="label_ar" value="{{ old('label_ar', $item->label_ar) }}" required dir="rtl">
                @error('label_ar')<small>{{ $message }}</small>@enderror
            </label>

            <label>Destination Type
                <select name="destination_type" required>
                    <option value="internal_route" @selected(old('destination_type', $item->destination_type) === 'internal_route')>Internal Named Route</option>
                    <option value="content_page" @selected(old('destination_type', $item->destination_type) === 'content_page')>Governed Content Page</option>
                    <option value="external_url" @selected(old('destination_type', $item->destination_type) === 'external_url')>External URL</option>
                </select>
            </label>

            <label>Internal Route Name
                <select name="route_name">
                    <option value="">Select Route...</option>
                    @foreach (['home', 'services', 'industries', 'process', 'results', 'about', 'resources', 'contact', 'portal'] as $r)
                        <option value="{{ $r }}" @selected(old('route_name', $item->route_name) === $r)>{{ $r }}</option>
                    @endforeach
                </select>
            </label>

            <label>Governed Content Page
                <select name="content_page_id">
                    <option value="">Select CMS Page...</option>
                    @foreach ($contentPages as $cp)
                        <option value="{{ $cp->id }}" @selected(old('content_page_id', $item->content_page_id) == $cp->id)>{{ $cp->title_en }} ({{ $cp->status }})</option>
                    @endforeach
                </select>
            </label>

            <label>External URL
                <input type="text" name="url" value="{{ old('url', $item->url) }}" placeholder="https://example.com/page">
                @error('url')<small>{{ $message }}</small>@enderror
            </label>

            <label>Sort Order
                <input type="number" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}" min="0">
            </label>

            <label>
                <input type="checkbox" name="target_blank" value="1" @checked(old('target_blank', $item->target_blank))>
                Open in New Tab (adds rel="noopener noreferrer")
            </label>

            <label>
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active))>
                Active / Visible
            </label>
        </div>

        <button type="submit" style="margin-top: 1.5rem;">Update Navigation Item</button>
    </form>
</main>
</body>
</html>
