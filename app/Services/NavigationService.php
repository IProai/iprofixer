<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\NavigationItem;
use App\Models\NavigationMenu;
use Illuminate\Support\Facades\Cache;

final class NavigationService
{
    /**
     * @return list<array{id: string, label: string, url: string, target_blank: bool, rel: ?string, children: list<array<string, mixed>>}>
     */
    public function getPublicMenu(string $location, string $locale = 'en'): array
    {
        $cacheKey = "nav_menu_{$location}_{$locale}";

        return Cache::remember($cacheKey, 3600, function () use ($location, $locale): array {
            $menu = NavigationMenu::query()
                ->where('location', $location)
                ->where('is_active', true)
                ->first();

            if (! $menu) {
                return [];
            }

            $items = NavigationItem::query()
                ->where('navigation_menu_id', $menu->id)
                ->where('is_active', true)
                ->with(['contentPage', 'children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
                ->orderBy('sort_order')
                ->get();

            $rootItems = $items->whereNull('parent_id');

            $result = [];
            foreach ($rootItems as $item) {
                if (! $this->isItemValidAndPublished($item)) {
                    continue;
                }

                $children = [];
                foreach ($item->children as $child) {
                    if ($this->isItemValidAndPublished($child)) {
                        $children[] = [
                            'id' => $child->id,
                            'label' => $child->getLabel($locale),
                            'url' => $child->resolveUrl(),
                            'target_blank' => $child->target_blank,
                            'rel' => $child->rel,
                        ];
                    }
                }

                $result[] = [
                    'id' => $item->id,
                    'label' => $item->getLabel($locale),
                    'url' => $item->resolveUrl(),
                    'target_blank' => $item->target_blank,
                    'rel' => $item->rel,
                    'children' => $children,
                ];
            }

            return $result;
        });
    }

    public function clearCache(): void
    {
        $locations = ['header', 'mobile', 'footer_explore', 'footer_start', 'footer_legal'];
        $locales = ['en', 'ar'];

        foreach ($locations as $loc) {
            foreach ($locales as $locale) {
                Cache::forget("nav_menu_{$loc}_{$locale}");
            }
        }
    }

    private function isItemValidAndPublished(NavigationItem $item): bool
    {
        if (! $item->is_active) {
            return false;
        }

        if ($item->destination_type === 'content_page') {
            $page = $item->contentPage;
            if (! $page || $page->trashed() || ! $page->isPublished()) {
                return false;
            }
        }

        if ($item->destination_type === 'external_url' && ! NavigationItem::isValidUrl($item->url)) {
            return false;
        }

        return true;
    }
}
