<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;

final class NavigationItem extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'navigation_menu_id',
        'parent_id',
        'label_en',
        'label_ar',
        'destination_type',
        'route_name',
        'content_page_id',
        'url',
        'sort_order',
        'is_active',
        'target_blank',
        'rel',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'target_blank' => 'boolean',
        ];
    }

    /** @return BelongsTo<NavigationMenu, $this> */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(NavigationMenu::class, 'navigation_menu_id');
    }

    /** @return BelongsTo<NavigationItem, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** @return HasMany<NavigationItem, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    /** @return BelongsTo<ContentPage, $this> */
    public function contentPage(): BelongsTo
    {
        return $this->belongsTo(ContentPage::class, 'content_page_id');
    }

    public function getLabel(string $locale = 'en'): string
    {
        return $locale === 'ar' ? $this->label_ar : $this->label_en;
    }

    public static function isValidUrl(?string $url): bool
    {
        if (empty($url)) {
            return true;
        }

        $trimmed = strtolower(trim($url));
        $prohibitedSchemes = ['javascript:', 'data:', 'vbscript:', 'file:'];

        foreach ($prohibitedSchemes as $scheme) {
            if (str_starts_with($trimmed, $scheme)) {
                return false;
            }
        }

        return true;
    }

    public function resolveUrl(): string
    {
        if ($this->destination_type === 'internal_route' && ! empty($this->route_name)) {
            if (Route::has($this->route_name)) {
                return route($this->route_name);
            }

            return match ($this->route_name) {
                'home' => route('home'),
                'services' => route('services'),
                'industries' => route('industries'),
                'process' => route('process'),
                'results' => route('results'),
                'about' => route('about'),
                'resources' => route('resources'),
                'contact' => route('contact'),
                'portal' => route('portal'),
                default => '#',
            };
        }

        if ($this->destination_type === 'content_page' && $this->content_page_id) {
            $page = $this->contentPage;
            if ($page && $page->isPublished()) {
                if ($page->type === 'service') {
                    return route('services.show', $page->slug);
                }
                if ($page->type === 'industry') {
                    return route('industries.show', $page->slug);
                }
                if (Route::has($page->slug)) {
                    return route($page->slug);
                }

                return url("/{$page->slug}");
            }

            return '#';
        }

        if ($this->destination_type === 'external_url' && ! empty($this->url)) {
            return self::isValidUrl($this->url) ? $this->url : '#';
        }

        return '#';
    }
}
