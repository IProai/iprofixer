<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class NavigationMenu extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'location',
        'name_en',
        'name_ar',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** @return HasMany<NavigationItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(NavigationItem::class)->orderBy('sort_order');
    }

    /** @return HasMany<NavigationItem, $this> */
    public function rootItems(): HasMany
    {
        return $this->items()->whereNull('parent_id')->where('is_active', true);
    }
}
