<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Organization>
 */
final class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        return [
            'group_id' => null,
            'market_id' => null,
            'name' => $this->faker->company(),
            'type' => $this->faker->randomElement(['prospect', 'client', 'partner']),
            'website' => $this->faker->optional()->url(),
            'phone' => $this->faker->optional()->phoneNumber(),
            'email' => $this->faker->optional()->companyEmail(),
            'address' => [
                'city' => $this->faker->city(),
                'country' => 'AE',
            ],
            'duplicate_status' => 'none',
            'duplicate_of_id' => null,
            'notes' => null,
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }

    public function prospect(): self
    {
        return $this->state(['type' => 'prospect']);
    }

    public function client(): self
    {
        return $this->state(['type' => 'client']);
    }
}
