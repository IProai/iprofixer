<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
final class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'property_id' => null,
            'salutation' => $this->faker->optional()->randomElement(['Mr.', 'Ms.', 'Dr.']),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->optional()->safeEmail(),
            'phone' => $this->faker->optional()->phoneNumber(),
            'job_title' => $this->faker->optional()->jobTitle(),
            'role_type' => $this->faker->randomElement(['decision_maker', 'influencer', 'stakeholder', 'procurement', 'fb_manager']),
            'is_primary' => false,
            'locale' => 'en',
            'notes' => null,
            'duplicate_status' => 'none',
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }

    public function primary(): self
    {
        return $this->state(['is_primary' => true]);
    }
}
