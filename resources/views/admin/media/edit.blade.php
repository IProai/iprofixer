<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Media Asset · IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">Content & Growth Console</p>
            <h1>Edit Media Asset</h1>
        </div>
        <a href="{{ route('admin.media.index') }}">Back to Media Library</a>
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

    <div class="admin-media-edit-layout" style="display: grid; grid-template-columns: 320px 1fr; gap: 2rem;">
        <div class="admin-media-preview-panel">
            @if ($medium->isImage())
                <div style="position: relative; width: 100%; max-height: 300px; overflow: hidden; border-radius: 8px; border: 1px solid #ccc;">
                    <img src="{{ $medium->getUrl() }}" alt="{{ $medium->alt_text_en ?? 'Preview' }}" style="width: 100%; height: auto; display: block;">
                    <div style="position: absolute; left: {{ $medium->focal_x * 100 }}%; top: {{ $medium->focal_y * 100 }}%; width: 16px; height: 16px; margin-left: -8px; margin-top: -8px; border: 2px solid red; border-radius: 50%; background: rgba(255,0,0,0.4);" title="Focal Point ({{ $medium->focal_x }}, {{ $medium->focal_y }})"></div>
                </div>
            @endif

            <dl style="margin-top: 1rem;">
                <dt>Original Filename</dt>
                <dd>{{ $medium->original_name }}</dd>

                <dt>File Size / MIME</dt>
                <dd>{{ round($medium->size_bytes / 1024, 1) }} KB ({{ $medium->mime_type }})</dd>

                <dt>SHA-256 Checksum</dt>
                <dd style="word-break: break-all; font-family: monospace; font-size: 0.8rem;">{{ $medium->checksum }}</dd>

                <dt>Status</dt>
                <dd>
                    <span class="badge {{ $medium->usage_status === 'approved' ? 'badge-success' : 'badge-warning' }}">
                        {{ ucfirst($medium->usage_status) }}
                    </span>
                </dd>
            </dl>

            <section class="admin-usage-panel" style="margin-top: 1.5rem;">
                <h3>CMS Usage Status</h3>
                @if (count($references) > 0)
                    <p style="color: #b91c1c; font-weight: bold;">This asset is currently in active use across {{ count($references) }} location(s):</p>
                    <ul>
                        @foreach ($references as $ref)
                            <li>{{ $ref['type'] }}: {{ $ref['label'] }}</li>
                        @endforeach
                    </ul>
                @else
                    <p style="color: #047857;">Unreferenced asset (safe for removal if authorized).</p>
                @endif
            </section>
        </div>

        <div>
            <form method="post" action="{{ route('admin.media.update', $medium) }}">
                @csrf
                @method('put')

                <div class="admin-form-grid">
                    <div class="full-field">
                        <label>
                            <input type="checkbox" name="is_decorative" value="1" @checked(old('is_decorative', $medium->is_decorative))>
                            This is a purely decorative image (does not require accessibility alt text)
                        </label>
                    </div>

                    <label>English Alt Text
                        <input name="alt_text_en" value="{{ old('alt_text_en', $medium->alt_text_en) }}" placeholder="Meaningful description for English screen readers">
                        @error('alt_text_en')<small>{{ $message }}</small>@enderror
                    </label>

                    <label dir="rtl">نص التوضيح بالعربية
                        <input name="alt_text_ar" value="{{ old('alt_text_ar', $medium->alt_text_ar) }}" dir="rtl" placeholder="وصف معبر لقارئات الشاشة باللغة العربية">
                        @error('alt_text_ar')<small>{{ $message }}</small>@enderror
                    </label>

                    <label>English Caption
                        <textarea name="caption_en" rows="2">{{ old('caption_en', $medium->caption_en) }}</textarea>
                    </label>

                    <label dir="rtl">التعليق بالعربية
                        <textarea name="caption_ar" rows="2" dir="rtl">{{ old('caption_ar', $medium->caption_ar) }}</textarea>
                    </label>

                    <label>Source / Owner
                        <input name="source_owner" value="{{ old('source_owner', $medium->source_owner) }}">
                    </label>

                    <div class="form-row">
                        <label>Focal Point X (0.0 to 1.0)
                            <input type="number" step="0.01" min="0" max="1" name="focal_x" value="{{ old('focal_x', $medium->focal_x) }}">
                        </label>
                        <label>Focal Point Y (0.0 to 1.0)
                            <input type="number" step="0.01" min="0" max="1" name="focal_y" value="{{ old('focal_y', $medium->focal_y) }}">
                        </label>
                    </div>
                </div>

                <button type="submit">Update Metadata</button>
            </form>

            <hr style="margin: 2rem 0;">

            <div style="display: flex; gap: 1rem;">
                <form method="post" action="{{ route('admin.media.destroy', $medium) }}" onsubmit="return confirm('Archive this media asset?')">
                    @csrf
                    @method('delete')
                    <button type="submit" class="button button-warning">Archive Asset</button>
                </form>

                <form method="post" action="{{ route('admin.media.force-delete', $medium->id) }}" onsubmit="return confirm('Permanently delete this media file from disk?')">
                    @csrf
                    @method('delete')
                    <button type="submit" class="button button-danger" @disabled(count($references) > 0)>
                        Permanently Delete {{ count($references) > 0 ? '(Blocked: In Use)' : '' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>
</body>
</html>
