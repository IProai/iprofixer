<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;

final class RedirectRule extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'source_path',
        'destination_type',
        'destination_path',
        'route_name',
        'content_page_id',
        'status_code',
        'is_active',
        'locale',
        'note',
        'hit_count',
        'last_hit_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'status_code' => 'integer',
            'is_active' => 'boolean',
            'hit_count' => 'integer',
            'last_hit_at' => 'immutable_datetime',
        ];
    }

    /** @return BelongsTo<ContentPage, $this> */
    public function contentPage(): BelongsTo
    {
        return $this->belongsTo(ContentPage::class, 'content_page_id');
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function normalizePath(string $path): string
    {
        $parsed = parse_url(trim($path), PHP_URL_PATH);
        $pathOnly = is_string($parsed) ? $parsed : $path;

        $pathOnly = '/'.ltrim($pathOnly, '/');
        if ($pathOnly !== '/') {
            $pathOnly = rtrim($pathOnly, '/');
        }

        return strtolower($pathOnly);
    }

    public static function isProtectedPath(string $path): bool
    {
        $normalized = self::normalizePath($path);
        $protectedPrefixes = [
            '/admin',
            '/portal',
            '/login',
            '/register',
            '/api',
            '/health',
            '/ready',
            '/sitemap.xml',
            '/robots.txt',
        ];

        foreach ($protectedPrefixes as $prefix) {
            if ($normalized === $prefix || str_starts_with($normalized, "{$prefix}/")) {
                return true;
            }
        }

        return false;
    }

    public static function isValidDestination(?string $destination): bool
    {
        if (empty($destination)) {
            return false;
        }

        $lower = strtolower(trim($destination));
        $prohibitedSchemes = ['javascript:', 'data:', 'vbscript:', 'file:'];

        foreach ($prohibitedSchemes as $scheme) {
            if (str_starts_with($lower, $scheme)) {
                return false;
            }
        }

        return true;
    }

    public function resolveDestinationUrl(): string
    {
        if ($this->destination_type === 'internal_route' && ! empty($this->route_name)) {
            if (Route::has($this->route_name)) {
                return route($this->route_name);
            }
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

                return url("/{$page->slug}");
            }
        }

        if (! empty($this->destination_path) && self::isValidDestination($this->destination_path)) {
            if (str_starts_with($this->destination_path, 'http://') || str_starts_with($this->destination_path, 'https://')) {
                return $this->destination_path;
            }

            return url($this->destination_path);
        }

        return url('/');
    }

    public function recordHit(): void
    {
        $this->timestamps = false;
        $this->increment('hit_count');
        $this->update(['last_hit_at' => now()]);
        $this->timestamps = true;
    }
}
