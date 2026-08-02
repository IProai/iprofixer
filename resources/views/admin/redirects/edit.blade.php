<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Redirect Rule · IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">Content & Growth Console</p>
            <h1>Edit Redirect Rule</h1>
        </div>
        <a href="{{ route('admin.redirects.index') }}">Back to Redirect Workspace</a>
    </header>

    @if ($errors->any())
        <div role="alert" class="admin-alert admin-alert-error" style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ route('admin.redirects.update', $rule) }}" style="max-width: 600px; margin-top: 1.5rem;">
        @csrf
        @method('put')

        <div style="display: grid; gap: 1rem;">
            <label>Source Path
                <input name="source_path" value="{{ old('source_path', $rule->source_path) }}" required placeholder="/old-path">
                @error('source_path')<small style="color:red;">{{ $message }}</small>@enderror
            </label>

            <label>Destination Type
                <select name="destination_type" required>
                    <option value="custom_url" @selected(old('destination_type', $rule->destination_type) === 'custom_url')>Custom Path / URL</option>
                    <option value="internal_route" @selected(old('destination_type', $rule->destination_type) === 'internal_route')>Internal Named Route</option>
                    <option value="content_page" @selected(old('destination_type', $rule->destination_type) === 'content_page')>Governed Content Page</option>
                </select>
            </label>

            <label>Destination Path / URL
                <input name="destination_path" value="{{ old('destination_path', $rule->destination_path) }}" required placeholder="/services or https://...">
                @error('destination_path')<small style="color:red;">{{ $message }}</small>@enderror
            </label>

            <label>Internal Route Name
                <select name="route_name">
                    <option value="">Select Route...</option>
                    @foreach (['home', 'services', 'industries', 'process', 'results', 'about', 'resources', 'contact', 'portal'] as $r)
                        <option value="{{ $r }}" @selected(old('route_name', $rule->route_name) === $r)>{{ $r }}</option>
                    @endforeach
                </select>
            </label>

            <label>Governed Content Page
                <select name="content_page_id">
                    <option value="">Select CMS Page...</option>
                    @foreach ($contentPages as $cp)
                        <option value="{{ $cp->id }}" @selected(old('content_page_id', $rule->content_page_id) == $cp->id)>{{ $cp->title_en }} ({{ $cp->status }})</option>
                    @endforeach
                </select>
            </label>

            <label>Redirect Type / Status Code
                <select name="status_code" required>
                    <option value="301" @selected(old('status_code', $rule->status_code) == 301)>301 Permanent Redirect</option>
                    <option value="302" @selected(old('status_code', $rule->status_code) == 302)>302 Temporary Redirect</option>
                </select>
            </label>

            <label>Administrative Note
                <textarea name="note" rows="2">{{ old('note', $rule->note) }}</textarea>
            </label>

            <label>
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $rule->is_active))>
                Active Rule
            </label>
        </div>

        <button type="submit" style="margin-top: 1.5rem;">Update Redirect Rule</button>
    </form>
</main>
</body>
</html>
