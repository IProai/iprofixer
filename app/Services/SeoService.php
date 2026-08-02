<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ContentPage;
use Illuminate\Support\Facades\Route;

final class SeoService
{
    /**
     * @return array{
     *   title: string,
     *   description: string,
     *   canonical: string,
     *   robots: string,
     *   og_title: string,
     *   og_description: string,
     *   og_image: string,
     *   hreflang: array<string, string>,
     *   structured_data: array<string, mixed>
     * }
     */
    public function getMetadata(?ContentPage $page = null, ?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $ar = $locale === 'ar';
        $siteName = (string) config('app.name', 'IProFixer');
        $baseUrl = rtrim((string) config('app.url', 'http://127.0.0.1:8000'), '/');

        $translation = $page ? $page->translation($locale) : null;

        // Title Hierarchy: Governed SEO Title -> Page Title -> Default Site Title
        $title = $translation?->seo_title
            ?? $translation?->title
            ?? ($ar ? 'آي برو فيكسر · عناية متخصصة بأصول الضيافة' : 'IProFixer · Specialist Hospitality Asset Care');

        // Description Hierarchy: Governed SEO Description -> Summary -> Default Description
        $description = $translation?->seo_description
            ?? $translation?->summary
            ?? ($ar
                ? 'عناية متخصصة وموثقة بأصول الفنادق والمطاعم والضيافة في الإمارات والسعودية.'
                : 'Specialist certified hospitality asset care across luxury hotels, restaurants and catering groups in UAE & KSA.');

        // Canonical URL
        $canonical = $this->getCanonicalUrl($page, $locale);

        // Robots Hierarchy & Protection
        $isNoindex = false;
        $isNofollow = false;

        if (config('app.env') !== 'production') {
            $isNoindex = true;
            $isNofollow = true;
        }

        $currentRouteName = Route::currentRouteName();
        if ($currentRouteName && (
            str_starts_with($currentRouteName, 'admin.') ||
            str_starts_with($currentRouteName, 'portal.') ||
            in_array($currentRouteName, ['login', 'register', 'rfq.store'], true)
        )) {
            $isNoindex = true;
            $isNofollow = true;
        }

        if ($translation) {
            if ($translation->is_noindex) {
                $isNoindex = true;
            }
            if ($translation->is_nofollow) {
                $isNofollow = true;
            }
        }

        if ($page && (! $page->isPublished() || $page->trashed())) {
            $isNoindex = true;
        }

        $robots = sprintf('%s, %s', $isNoindex ? 'noindex' : 'index', $isNofollow ? 'nofollow' : 'follow');

        // OpenGraph
        $ogTitle = $translation?->og_title ?? $title;
        $ogDescription = $translation?->og_description ?? $description;
        $ogImage = $translation?->ogImage?->getUrl() ?? "{$baseUrl}/assets/og-default.jpg";

        // Hreflang Alternates
        $hreflang = $this->getHreflangAlternates($page, $locale);

        // Structured Data
        $structuredData = $this->getStructuredData($page, $locale, $title, $description, $canonical);

        return [
            'title' => $this->sanitizeText($title),
            'description' => $this->sanitizeText($description),
            'canonical' => $canonical,
            'robots' => $robots,
            'og_title' => $this->sanitizeText($ogTitle),
            'og_description' => $this->sanitizeText($ogDescription),
            'og_image' => $ogImage,
            'hreflang' => $hreflang,
            'structured_data' => $structuredData,
        ];
    }

    public function getCanonicalUrl(?ContentPage $page = null, ?string $locale = null): string
    {
        $baseUrl = rtrim((string) config('app.url', 'http://127.0.0.1:8000'), '/');
        $locale = $locale ?? app()->getLocale();

        if ($page && $page->isPublished()) {
            $path = match ($page->type) {
                'service' => "/services/{$page->slug}",
                'industry' => "/industries/{$page->slug}",
                default => "/{$page->slug}",
            };

            return "{$baseUrl}{$path}";
        }

        $requestPath = parse_url((string) request()->getRequestUri(), PHP_URL_PATH) ?? '/';
        $normalizedPath = '/'.ltrim($requestPath, '/');

        return "{$baseUrl}{$normalizedPath}";
    }

    /**
     * @return array<string, string>
     */
    public function getHreflangAlternates(?ContentPage $page = null, string $currentLocale = 'en'): array
    {
        $baseUrl = rtrim((string) config('app.url', 'http://127.0.0.1:8000'), '/');
        $alternates = [];

        if ($page) {
            if ($page->isTranslationApproved('en') && $page->isPublished()) {
                $path = $page->type === 'service' ? "/services/{$page->slug}" : ($page->type === 'industry' ? "/industries/{$page->slug}" : "/{$page->slug}");
                $alternates['en'] = "{$baseUrl}{$path}";
            }
            if ($page->isTranslationApproved('ar') && $page->isPublished()) {
                $path = $page->type === 'service' ? "/services/{$page->slug}" : ($page->type === 'industry' ? "/industries/{$page->slug}" : "/{$page->slug}");
                $alternates['ar'] = "{$baseUrl}{$path}";
            }
        } else {
            $requestPath = parse_url((string) request()->getRequestUri(), PHP_URL_PATH) ?? '/';
            $path = '/'.ltrim($requestPath, '/');

            $alternates['en'] = "{$baseUrl}{$path}";
            $alternates['ar'] = "{$baseUrl}{$path}";
        }

        if (isset($alternates['en'])) {
            $alternates['x-default'] = $alternates['en'];
        }

        return $alternates;
    }

    /**
     * @return array<string, mixed>
     */
    public function getStructuredData(
        ?ContentPage $page,
        string $locale,
        string $title,
        string $description,
        string $canonicalUrl,
    ): array {
        $baseUrl = rtrim((string) config('app.url', 'http://127.0.0.1:8000'), '/');

        $orgSchema = [
            '@type' => 'Organization',
            '@id' => "{$baseUrl}/#organization",
            'name' => 'IProFixer',
            'url' => $baseUrl,
            'logo' => "{$baseUrl}/assets/logo.png",
            'description' => 'Specialist hospitality asset care across the UAE & KSA.',
        ];

        $siteSchema = [
            '@type' => 'WebSite',
            '@id' => "{$baseUrl}/#website",
            'url' => $baseUrl,
            'name' => 'IProFixer',
            'inLanguage' => $locale === 'ar' ? 'ar-AE' : 'en-US',
            'publisher' => ['@id' => "{$baseUrl}/#organization"],
        ];

        $pageSchema = [
            '@type' => 'WebPage',
            '@id' => "{$canonicalUrl}#webpage",
            'url' => $canonicalUrl,
            'name' => $this->sanitizeText($title),
            'description' => $this->sanitizeText($description),
            'inLanguage' => $locale === 'ar' ? 'ar-AE' : 'en-US',
            'isPartOf' => ['@id' => "{$baseUrl}/#website"],
        ];

        $breadcrumbSchema = [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => $locale === 'ar' ? 'الرئيسية' : 'Home',
                    'item' => $baseUrl,
                ],
            ],
        ];

        $schemas = [
            '@context' => 'https://schema.org',
            '@graph' => [
                $orgSchema,
                $siteSchema,
                $pageSchema,
                $breadcrumbSchema,
            ],
        ];

        if ($page && $page->type === 'service') {
            $schemas['@graph'][] = [
                '@type' => 'Service',
                '@id' => "{$canonicalUrl}#service",
                'name' => $this->sanitizeText($title),
                'description' => $this->sanitizeText($description),
                'provider' => ['@id' => "{$baseUrl}/#organization"],
            ];
        }

        return $schemas;
    }

    private function sanitizeText(string $text): string
    {
        return e(trim(strip_tags($text)));
    }
}
