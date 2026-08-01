<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Upload Media Asset · IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="admin-shell">
    <header class="admin-header">
        <div>
            <p class="eyebrow">Content & Growth Console</p>
            <h1>Upload Media Asset</h1>
        </div>
        <a href="{{ route('admin.media.index') }}">Back to Media Library</a>
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

    <form method="post" action="{{ route('admin.media.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="admin-form-grid">
            <label class="full-field">Select Image File (JPG, PNG, WebP, AVIF, GIF — max 10MB)
                <input type="file" name="file" accept="image/jpeg,image/png,image/webp,image/avif,image/gif" required>
                @error('file')<small>{{ $message }}</small>@enderror
            </label>

            <div class="full-field">
                <label>
                    <input type="checkbox" name="is_decorative" value="1" @checked(old('is_decorative'))>
                    This is a purely decorative image (does not require accessibility alt text)
                </label>
            </div>

            <label>English Alt Text
                <input name="alt_text_en" value="{{ old('alt_text_en') }}" placeholder="Meaningful description for English screen readers">
                @error('alt_text_en')<small>{{ $message }}</small>@enderror
            </label>

            <label dir="rtl">نص التوضيح بالعربية
                <input name="alt_text_ar" value="{{ old('alt_text_ar') }}" dir="rtl" placeholder="وصف معبر لقارئات الشاشة باللغة العربية">
                @error('alt_text_ar')<small>{{ $message }}</small>@enderror
            </label>

            <label>English Caption
                <textarea name="caption_en" rows="2">{{ old('caption_en') }}</textarea>
            </label>

            <label dir="rtl">التعليق بالعربية
                <textarea name="caption_ar" rows="2" dir="rtl">{{ old('caption_ar') }}</textarea>
            </label>

            <label>Source / Owner
                <input name="source_owner" value="{{ old('source_owner') }}" placeholder="e.g. Internal Photography">
            </label>

            <div class="form-row">
                <label>Focal Point X (0.0 to 1.0)
                    <input type="number" step="0.01" min="0" max="1" name="focal_x" value="{{ old('focal_x', '0.5') }}">
                </label>
                <label>Focal Point Y (0.0 to 1.0)
                    <input type="number" step="0.01" min="0" max="1" name="focal_y" value="{{ old('focal_y', '0.5') }}">
                </label>
            </div>
        </div>

        <button type="submit">Upload & Save Metadata</button>
    </form>
</main>
</body>
</html>
