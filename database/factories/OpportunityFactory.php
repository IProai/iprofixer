<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Opportunity;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Opportunity>
 */
final class OpportunityFactory extends Factory
{
    protected $model = Opportunity::class;

    public function definition(): array
    {
        return [
            'lead_id' => null,
            'organization_id' => Organization::factory(),
            'property_id' => null,
            'contact_id' => null,
            'assigned_to' => User::factory(),
            'title' => $this->faker->company().' — Cutlery Care Program',
            'stage' => 'discovery',
            'probability' => 20,
            'service_code' => $this->faker->optional()->randomElement(['cutlery-restoration', 'hollowware-care', 'recurring-care-plans']),
            'estimated_value' => $this->faker->optional()->randomFloat(2, 5000, 200000),
            'currency_code' => 'AED',
            'expected_close_date' => $this->faker->optional()->dateTimeBetween('+1 month', '+6 months')?->format('Y-m-d'),
            'next_action' => 'Schedule discovery call',
            'next_action_due_at' => now()->addDays(3),
            'loss_reason' => null,
            'loss_notes' => null,
            'notes' => null,
            'won_at' => null,
            'lost_at' => null,
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }

    public function won(): self
    {
        return $this->state([
            'stage' => 'won',
            'probability' => 100,
            'won_at' => now()->subDay(),
        ]);
    }

    public function lost(): self
    {
        return $this->state([
            'stage' => 'lost',
            'probability' => 0,
            'loss_reason' => 'price',
            'loss_notes' => 'Client chose a lower-cost provider.',
            'lost_at' => now()->subDay(),
        ]);
    }
}
