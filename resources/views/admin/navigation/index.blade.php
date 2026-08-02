<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Navigation & Footer Governance · IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">Content & Growth Console</p>
            <h1>Navigation & Footer Governance</h1>
        </div>
    </header>

    @if (session('status'))
        <p role="status">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <div role="alert" class="admin-alert admin-alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="admin-navigation-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <div>
            @foreach ($menus as $menu)
                <section class="admin-menu-section" style="margin-bottom: 2rem; border: 1px solid #ccc; padding: 1.5rem; border-radius: 8px;">
                    <div style="display: flex; justify-space-between; align-items: center;">
                        <h2>{{ $menu->name_en }} / {{ $menu->name_ar }} <small>({{ $menu->location }})</small></h2>
                        <span class="badge {{ $menu->is_active ? 'badge-success' : 'badge-warning' }}">
                            {{ $menu->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="admin-table-wrap" style="margin-top: 1rem;">
                        <table>
                            <thead>
                            <tr>
                                <th>Order</th>
                                <th>English Label</th>
                                <th>Arabic Label</th>
                                <th>Destination</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($menu->items as $item)
                                <tr>
                                    <td>{{ $item->sort_order }}</td>
                                    <td>
                                        @if ($item->parent_id) — @endif
                                        <strong>{{ $item->label_en }}</strong>
                                    </td>
                                    <td dir="rtl">{{ $item->label_ar }}</td>
                                    <td>
                                        <small>{{ ucfirst(str_replace('_', ' ', $item->destination_type)) }}:</small><br>
                                        <code>{{ $item->resolveUrl() }}</code>
                                    </td>
                                    <td>
                                        <form method="post" action="{{ route('admin.navigation.items.toggle', $item) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="button-small">
                                                {{ $item->is_active ? 'Active' : 'Disabled' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.navigation.items.edit', $item) }}">Edit</a> |
                                        <form method="post" action="{{ route('admin.navigation.items.destroy', $item) }}" style="display:inline;" onsubmit="return confirm('Delete this navigation item?')">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" style="background:none; border:none; color:red; cursor:pointer;">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6">No menu items configured yet for this location.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @endforeach
        </div>

        <div>
            <section class="admin-card" style="border: 1px solid #ccc; padding: 1.5rem; border-radius: 8px;">
                <h3>Add Navigation Item</h3>
                <form method="post" action="{{ route('admin.navigation.items.store') }}">
                    @csrf

                    <div class="admin-form-grid">
                        <label>Menu Location
                            <select name="navigation_menu_id" required>
                                @foreach ($menus as $menu)
                                    <option value="{{ $menu->id }}">{{ $menu->name_en }} ({{ $menu->location }})</option>
                                @endforeach
                            </select>
                        </label>

                        <label>English Label
                            <input name="label_en" value="{{ old('label_en') }}" required placeholder="e.g. Services">
                        </label>

                        <label dir="rtl">الاسم بالعربية
                            <input name="label_ar" value="{{ old('label_ar') }}" required dir="rtl" placeholder="مثل: الخدمات">
                        </label>

                        <label>Destination Type
                            <select name="destination_type" required>
                                <option value="internal_route" @selected(old('destination_type') === 'internal_route')>Internal Named Route</option>
                                <option value="content_page" @selected(old('destination_type') === 'content_page')>Governed Content Page</option>
                                <option value="external_url" @selected(old('destination_type') === 'external_url')>External URL</option>
                            </select>
                        </label>

                        <label>Internal Route Name
                            <select name="route_name">
                                <option value="">Select Route...</option>
                                @foreach (['home', 'services', 'industries', 'process', 'results', 'about', 'resources', 'contact', 'portal'] as $r)
                                    <option value="{{ $r }}">{{ $r }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label>Governed Content Page
                            <select name="content_page_id">
                                <option value="">Select CMS Page...</option>
                                @foreach ($contentPages as $cp)
                                    <option value="{{ $cp->id }}">{{ $cp->title_en }} ({{ $cp->status }})</option>
                                @endforeach
                            </select>
                        </label>

                        <label>External URL
                            <input type="text" name="url" value="{{ old('url') }}" placeholder="https://example.com/page">
                        </label>

                        <label>Sort Order
                            <input type="number" name="sort_order" value="{{ old('sort_order', 1) }}" min="0">
                        </label>

                        <label>
                            <input type="checkbox" name="target_blank" value="1" @checked(old('target_blank'))>
                            Open in New Tab (adds rel="noopener noreferrer")
                        </label>
                    </div>

                    <button type="submit" style="margin-top: 1rem;">Add Menu Item</button>
                </form>
            </section>
        </div>
    </div>
</main>
</body>
</html>
