<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Lead;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
final class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'form_submission_id' => null,
            'organization_id' => Organization::factory(),
            'property_id' => null,
            'contact_id' => null,
            'assigned_to' => null,
            'status' => 'new',
            'source' => 'rfq',
            'source_detail' => null,
            'service_code' => $this->faker->optional()->randomElement(['cutlery-restoration', 'hollowware-care', 'asset-condition-review', 'recurring-care-plans']),
            'property_type' => $this->faker->optional()->randomElement(['hotel', 'restaurant', 'catering']),
            'urgency' => $this->faker->optional()->randomElement(['low', 'normal', 'high']),
            'estimated_quantity' => $this->faker->optional()->numberBetween(50, 2000),
            'budget_indication' => null,
            'qualification_notes' => null,
            'disqualification_reason' => null,
            'qualified_at' => null,
            'disqualified_at' => null,
            'converted_at' => null,
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }

    public function qualified(): self
    {
        return $this->state([
            'status' => 'qualified',
            'qualified_at' => now()->subDay(),
        ]);
    }

    public function disqualified(): self
    {
        return $this->state([
            'status' => 'disqualified',
            'disqualification_reason' => 'no_budget',
            'disqualified_at' => now()->subDay(),
        ]);
    }

    public function converted(): self
    {
        return $this->state([
            'status' => 'converted',
            'qualified_at' => now()->subDays(3),
            'converted_at' => now()->subDay(),
        ]);
    }
}
