<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\RedirectRule;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<RedirectRule>
 */
final class RedirectRuleFactory extends Factory
{
    protected $model = RedirectRule::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $source = '/old-path-'.Str::random(6);

        return [
            'id' => (string) Str::uuid(),
            'source_path' => $source,
            'destination_type' => 'custom_url',
            'destination_path' => '/services',
            'route_name' => null,
            'content_page_id' => null,
            'status_code' => 301,
            'is_active' => true,
            'locale' => null,
            'note' => 'Legacy path redirect',
            'hit_count' => 0,
            'last_hit_at' => null,
        ];
    }
}
