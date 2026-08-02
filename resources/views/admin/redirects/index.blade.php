<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redirect Governance · IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">Content & Growth Console</p>
            <h1>SEO & Redirect Governance</h1>
        </div>
    </header>

    @if (session('status'))
        <p role="status" style="color: green; font-weight: bold;">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <div role="alert" class="admin-alert admin-alert-error" style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-top: 1rem;">
        <div>
            <section style="border: 1px solid #ccc; padding: 1.5rem; border-radius: 8px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2>Active & Governed Redirect Rules</h2>
                    <form method="get" action="{{ route('admin.redirects.index') }}" style="display: flex; gap: 0.5rem;">
                        <input name="search" value="{{ request('search') }}" placeholder="Search source or destination...">
                        <select name="status">
                            <option value="">All Statuses</option>
                            <option value="active" @selected(request('status') === 'active')>Active Only</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Inactive Only</option>
                        </select>
                        <button type="submit">Filter</button>
                    </form>
                </div>

                <div class="admin-table-wrap">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                        <tr style="border-bottom: 2px solid #ccc;">
                            <th style="text-align: left;">Source Path</th>
                            <th style="text-align: left;">Destination</th>
                            <th>Status</th>
                            <th>Hits</th>
                            <th>Active</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($redirects as $rule)
                            <tr style="border-bottom: 1px solid #eee;">
                                <td><code>{{ $rule->source_path }}</code></td>
                                <td>
                                    <small>{{ ucfirst(str_replace('_', ' ', $rule->destination_type)) }}:</small><br>
                                    <code>{{ $rule->destination_path }}</code>
                                </td>
                                <td><strong>{{ $rule->status_code }}</strong></td>
                                <td>{{ $rule->hit_count }}</td>
                                <td>
                                    <form method="post" action="{{ route('admin.redirects.toggle', $rule) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit">{{ $rule->is_active ? 'Active' : 'Disabled' }}</button>
                                    </form>
                                </td>
                                <td>
                                    <a href="{{ route('admin.redirects.edit', $rule) }}">Edit</a> |
                                    <form method="post" action="{{ route('admin.redirects.destroy', $rule) }}" style="display:inline;" onsubmit="return confirm('Delete this redirect rule?')">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" style="color:red; background:none; border:none; cursor:pointer;">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No redirect rules found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 1rem;">
                    {{ $redirects->links() }}
                </div>
            </section>
        </div>

        <div>
            <section style="border: 1px solid #ccc; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                <h3>Add Governed Redirect Rule</h3>
                <form method="post" action="{{ route('admin.redirects.store') }}">
                    @csrf

                    <div style="display: grid; gap: 1rem;">
                        <label>Source Path (e.g. /old-services)
                            <input name="source_path" value="{{ old('source_path') }}" required placeholder="/old-path">
                        </label>

                        <label>Destination Type
                            <select name="destination_type" required>
                                <option value="custom_url" @selected(old('destination_type') === 'custom_url')>Custom Path / URL</option>
                                <option value="internal_route" @selected(old('destination_type') === 'internal_route')>Internal Named Route</option>
                                <option value="content_page" @selected(old('destination_type') === 'content_page')>Governed Content Page</option>
                            </select>
                        </label>

                        <label>Destination Path / URL
                            <input name="destination_path" value="{{ old('destination_path') }}" required placeholder="/services or https://...">
                        </label>

                        <label>Internal Route Name (Optional)
                            <select name="route_name">
                                <option value="">Select Route...</option>
                                @foreach (['home', 'services', 'industries', 'process', 'results', 'about', 'resources', 'contact', 'portal'] as $r)
                                    <option value="{{ $r }}">{{ $r }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label>Governed Content Page (Optional)
                            <select name="content_page_id">
                                <option value="">Select CMS Page...</option>
                                @foreach ($contentPages as $cp)
                                    <option value="{{ $cp->id }}">{{ $cp->title_en }} ({{ $cp->status }})</option>
                                @endforeach
                            </select>
                        </label>

                        <label>Redirect Type / Status Code
                            <select name="status_code" required>
                                <option value="301" @selected(old('status_code', '301') == '301')>301 Permanent Redirect</option>
                                <option value="302" @selected(old('status_code') == '302')>302 Temporary Redirect</option>
                            </select>
                        </label>

                        <label>Administrative Note
                            <textarea name="note" rows="2" placeholder="Why this redirect was created...">{{ old('note') }}</textarea>
                        </label>

                        <label>
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))>
                            Active Rule
                        </label>
                    </div>

                    <button type="submit" style="margin-top: 1rem;">Create Redirect Rule</button>
                </form>
            </section>
        </div>
    </div>
</main>
</body>
</html>
