<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
final class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'market_id' => null,
            'name' => $this->faker->company().' '.$this->faker->randomElement(['Hotel', 'Restaurant', 'Resort']),
            'type' => $this->faker->randomElement(['hotel', 'restaurant', 'catering', 'events', 'other']),
            'address' => [
                'city' => $this->faker->city(),
                'country' => 'AE',
            ],
            'phone' => $this->faker->optional()->phoneNumber(),
            'notes' => null,
            'is_active' => true,
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }
}
