<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ContentPage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

final class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $xml = Cache::remember('sitemap_xml', 3600, function (): string {
            $baseUrl = rtrim((string) config('app.url', 'http://127.0.0.1:8000'), '/');

            $canonicalRoutes = [
                '/',
                '/services',
                '/industries',
                '/process',
                '/results',
                '/about',
                '/resources',
                '/contact',
            ];

            $urls = [];

            foreach ($canonicalRoutes as $routePath) {
                $loc = "{$baseUrl}{$routePath}";
                $urls[] = [
                    'loc' => $loc,
                    'lastmod' => now()->toIso8601String(),
                    'alternates' => [
                        'en' => $loc,
                        'ar' => $loc,
                    ],
                ];
            }

            $pages = ContentPage::query()
                ->where('status', 'published')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->with('translations')
                ->get();

            foreach ($pages as $page) {
                $enTrans = $page->translation('en');

                if ($enTrans && $enTrans->is_noindex) {
                    continue;
                }

                $path = match ($page->type) {
                    'service' => "/services/{$page->slug}",
                    'industry' => "/industries/{$page->slug}",
                    default => "/{$page->slug}",
                };

                $loc = "{$baseUrl}{$path}";
                $lastmod = ($page->updated_at ?? $page->published_at ?? now())->toIso8601String();

                $alternates = [];
                if ($page->isTranslationApproved('en')) {
                    $alternates['en'] = $loc;
                }
                if ($page->isTranslationApproved('ar')) {
                    $alternates['ar'] = $loc;
                }

                $urls[] = [
                    'loc' => $loc,
                    'lastmod' => $lastmod,
                    'alternates' => $alternates,
                ];
            }

            return $this->buildXml($urls);
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * @param  list<array{loc: string, lastmod: string, alternates: array<string, string>}>  $urls
     */
    private function buildXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.htmlspecialchars($url['loc'], ENT_XML1, 'UTF-8')."</loc>\n";
            $xml .= '    <lastmod>'.htmlspecialchars($url['lastmod'], ENT_XML1, 'UTF-8')."</lastmod>\n";

            foreach ($url['alternates'] as $locale => $altUrl) {
                $xml .= sprintf(
                    '    <xhtml:link rel="alternate" hreflang="%s" href="%s" />'."\n",
                    htmlspecialchars($locale, ENT_XML1, 'UTF-8'),
                    htmlspecialchars($altUrl, ENT_XML1, 'UTF-8')
                );
            }

            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
