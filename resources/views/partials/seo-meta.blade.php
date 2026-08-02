@php
    $seoService = app(\App\Services\SeoService::class);
    $seoPage = ($page ?? null) instanceof \App\Models\ContentPage ? $page : null;
    $seo = $seoService->getMetadata($seoPage, app()->getLocale());
    if ($isPreview ?? false) {
        $seo['robots'] = 'noindex, nofollow';
    }
@endphp
<title>{{ $seo['title'] }}</title>
<meta name="description" content="{{ $seo['description'] }}">
<link rel="canonical" href="{{ $seo['canonical'] }}">
<meta name="robots" content="{{ $seo['robots'] }}">

@foreach ($seo['hreflang'] as $lang => $url)
<link rel="alternate" hreflang="{{ $lang }}" href="{{ $url }}">
@endforeach

<meta property="og:title" content="{{ $seo['og_title'] }}">
<meta property="og:description" content="{{ $seo['og_description'] }}">
<meta property="og:url" content="{{ $seo['canonical'] }}">
<meta property="og:site_name" content="{{ config('app.name', 'IProFixer') }}">
<meta property="og:locale" content="{{ app()->getLocale() === 'ar' ? 'ar_AE' : 'en_US' }}">
<meta property="og:image" content="{{ $seo['og_image'] }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seo['og_title'] }}">
<meta name="twitter:description" content="{{ $seo['og_description'] }}">
<meta name="twitter:image" content="{{ $seo['og_image'] }}">

<script type="application/ld+json">
{!! json_encode($seo['structured_data'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
