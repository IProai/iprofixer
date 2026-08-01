<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\NavigationItem;
use App\Models\NavigationMenu;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<NavigationItem>
 */
final class NavigationItemFactory extends Factory
{
    protected $model = NavigationItem::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'navigation_menu_id' => NavigationMenu::factory(),
            'parent_id' => null,
            'label_en' => 'Services',
            'label_ar' => 'الخدمات',
            'destination_type' => 'internal_route',
            'route_name' => 'services',
            'content_page_id' => null,
            'url' => null,
            'sort_order' => 1,
            'is_active' => true,
            'target_blank' => false,
            'rel' => null,
        ];
    }
}
