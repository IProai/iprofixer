<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ContentPage;
use App\Models\RedirectRule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class RedirectService
{
    public function findActiveRedirect(string $requestPath): ?RedirectRule
    {
        $normalized = RedirectRule::normalizePath($requestPath);

        return RedirectRule::query()
            ->where('source_path', $normalized)
            ->where('is_active', true)
            ->first();
    }

    public function createAutoRedirect(ContentPage $page, string $oldSlug): ?RedirectRule
    {
        if ($oldSlug === $page->slug || ($page->status !== 'published' || $page->published_at === null)) {
            return null;
        }

        $oldPath = match ($page->type) {
            'service' => "/services/{$oldSlug}",
            'industry' => "/industries/{$oldSlug}",
            default => "/{$oldSlug}",
        };

        $newPath = match ($page->type) {
            'service' => "/services/{$page->slug}",
            'industry' => "/industries/{$page->slug}",
            default => "/{$page->slug}",
        };

        $normalizedSource = RedirectRule::normalizePath($oldPath);
        $normalizedDest = RedirectRule::normalizePath($newPath);

        if ($normalizedSource === $normalizedDest) {
            return null;
        }

        if (RedirectRule::isProtectedPath($normalizedSource)) {
            return null;
        }

        $rule = DB::transaction(function () use ($page, $normalizedSource, $newPath, $oldSlug): RedirectRule {
            $rule = RedirectRule::query()->updateOrCreate(
                ['source_path' => $normalizedSource],
                [
                    'destination_type' => 'content_page',
                    'destination_path' => $newPath,
                    'content_page_id' => $page->id,
                    'status_code' => 301,
                    'is_active' => true,
                    'note' => "Automatic 301 redirect created following CMS slug change from {$normalizedSource} to {$newPath}",
                ]
            );

            DB::table('audit_events')->insert([
                'id' => (string) Str::uuid(),
                'action' => 'redirect.auto_slug_created',
                'subject_type' => RedirectRule::class,
                'subject_id' => (string) $rule->id,
                'correlation_id' => (string) Str::uuid(),
                'before' => json_encode(['old_slug' => $oldSlug], JSON_THROW_ON_ERROR),
                'after' => json_encode(['new_slug' => $page->slug, 'rule_id' => $rule->id], JSON_THROW_ON_ERROR),
                'metadata' => json_encode(['source' => 'content_page.slug_update'], JSON_THROW_ON_ERROR),
                'occurred_at' => now(),
            ]);

            return $rule;
        });

        $this->clearSitemapCache();

        return $rule;
    }

    public function clearSitemapCache(): void
    {
        Cache::forget('sitemap_xml');
    }
}
