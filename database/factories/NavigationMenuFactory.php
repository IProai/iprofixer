<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\NavigationMenu;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<NavigationMenu>
 */
final class NavigationMenuFactory extends Factory
{
    protected $model = NavigationMenu::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'location' => 'header_'.Str::random(6),
            'name_en' => 'Header Navigation',
            'name_ar' => 'التنقل الرئيسي',
            'is_active' => true,
        ];
    }
}
