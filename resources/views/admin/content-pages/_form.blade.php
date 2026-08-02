@csrf

<div class="admin-form-grid">
    <label>Slug
        <input name="slug" value="{{ old('slug', $contentPage->slug ?? '') }}" required>
        @error('slug')<small>{{ $message }}</small>@enderror
    </label>

    <label>Type
        <select name="type" required>
            @foreach (['page', 'service', 'industry', 'resource'] as $type)
                <option value="{{ $type }}" @selected(old('type', $contentPage->type ?? 'page') === $type)>{{ ucfirst($type) }}</option>
            @endforeach
        </select>
    </label>

    <label>Status
        <select name="status" required>
            @foreach (['draft', 'review', 'approved', 'scheduled', 'published'] as $status)
                <option value="{{ $status }}" @selected(old('status', $contentPage->status ?? 'draft') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('status')<small>{{ $message }}</small>@enderror
    </label>

    <label>Scheduled For (UTC)
        <input type="datetime-local" name="scheduled_for" value="{{ old('scheduled_for', isset($contentPage->scheduled_for) ? $contentPage->scheduled_for->format('Y-m-d\TH:i') : '') }}">
        @error('scheduled_for')<small>{{ $message }}</small>@enderror
    </label>

    <div class="form-checkbox-group">
        <label>
            <input type="checkbox" name="approve_en" value="1" @checked(old('approve_en', isset($contentPage) && $contentPage->isTranslationApproved('en')))>
            Approve English Translation
        </label>

        <label dir="rtl">
            <input type="checkbox" name="approve_ar" value="1" @checked(old('approve_ar', isset($contentPage) && $contentPage->isTranslationApproved('ar')))>
            اعتماد الترجمة العربية
        </label>
    </div>

    <label>English title
        <input name="title_en" value="{{ old('title_en', $contentPage->title_en ?? '') }}" required>
        @error('title_en')<small>{{ $message }}</small>@enderror
    </label>

    <label dir="rtl">العنوان العربي
        <input name="title_ar" value="{{ old('title_ar', $contentPage->title_ar ?? '') }}" required dir="rtl">
        @error('title_ar')<small>{{ $message }}</small>@enderror
    </label>

    <label>English summary
        <textarea name="summary_en" rows="3">{{ old('summary_en', $contentPage->summary_en ?? '') }}</textarea>
    </label>

    <label dir="rtl">الملخص العربي
        <textarea name="summary_ar" rows="3" dir="rtl">{{ old('summary_ar', $contentPage->summary_ar ?? '') }}</textarea>
    </label>

    <label>English body
        <textarea name="body_en" rows="12" required>{{ old('body_en', $contentPage->body_en ?? '') }}</textarea>
        @error('body_en')<small>{{ $message }}</small>@enderror
    </label>

    <label dir="rtl">المحتوى العربي
        <textarea name="body_ar" rows="12" required dir="rtl">{{ old('body_ar', $contentPage->body_ar ?? '') }}</textarea>
        @error('body_ar')<small>{{ $message }}</small>@enderror
    </label>

    <label>SEO title (English)
        <input name="seo_title_en" value="{{ old('seo_title_en', $contentPage->seo_title_en ?? '') }}">
    </label>

    <label dir="rtl">عنوان SEO بالعربية
        <input name="seo_title_ar" value="{{ old('seo_title_ar', $contentPage->seo_title_ar ?? '') }}" dir="rtl">
    </label>

    <label>SEO description (English)
        <textarea name="seo_description_en" rows="3">{{ old('seo_description_en', $contentPage->seo_description_en ?? '') }}</textarea>
    </label>

    <label dir="rtl">وصف SEO بالعربية
        <textarea name="seo_description_ar" rows="3" dir="rtl">{{ old('seo_description_ar', $contentPage->seo_description_ar ?? '') }}</textarea>
    </label>
</div>

<button type="submit">Save content page</button>
