<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ContentPage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContentPage>
 */
final class ContentPageFactory extends Factory
{
    protected $model = ContentPage::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'slug' => $this->faker->unique()->slug(),
            'type' => 'page',
            'status' => 'draft',
            'published_at' => null,
            'scheduled_for' => null,
        ];
    }
}
