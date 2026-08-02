<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\OrganizationGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrganizationGroup>
 */
final class OrganizationGroupFactory extends Factory
{
    protected $model = OrganizationGroup::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company().' Group',
            'notes' => $this->faker->optional()->sentence(),
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }
}
