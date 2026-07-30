<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ContentPage;
use Illuminate\View\View;

final class PublicContentController extends Controller
{
    /** @var array<string, string> */
    private const PAGE_ROUTES = [
        'services' => 'services',
        'industries' => 'industries',
        'process' => 'process',
        'results' => 'results',
        'about' => 'about',
        'resources' => 'resources',
        'contact' => 'contact',
        'portal' => 'portal',
    ];

    /** @var array<string, list<string>> */
    private const DETAIL_SLUGS = [
        'service' => [
            'cutlery-restoration',
            'hollowware-care',
            'asset-condition-review',
            'recurring-care-plans',
        ],
        'industry' => [
            'hotels-resorts',
            'restaurants-groups',
            'catering-events',
            'procurement-operations',
        ],
    ];

    public function page(string $page): View
    {
        abort_unless(array_key_exists($page, self::PAGE_ROUTES), 404);

        return view('page', [
            'page' => self::PAGE_ROUTES[$page],
            'cmsPage' => $this->publishedContent('page', $page),
        ]);
    }

    public function service(string $service): View
    {
        abort_unless(in_array($service, self::DETAIL_SLUGS['service'], true), 404);

        return view('page', [
            'page' => 'service-detail',
            'slug' => $service,
            'cmsPage' => $this->publishedContent('service', $service),
        ]);
    }

    public function industry(string $industry): View
    {
        abort_unless(in_array($industry, self::DETAIL_SLUGS['industry'], true), 404);

        return view('page', [
            'page' => 'industry-detail',
            'slug' => $industry,
            'cmsPage' => $this->publishedContent('industry', $industry),
        ]);
    }

    private function publishedContent(string $type, string $slug): ?ContentPage
    {
        $content = ContentPage::query()
            ->where('type', $type)
            ->where('slug', $slug)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->first();

        return $content?->isPublished() ? $content : null;
    }
}
