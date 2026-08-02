<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<User> */
final class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'preferred_locale' => 'en',
            'email_verified_at' => new DateTimeImmutable,
            'password' => '$2y$12$4E5hKjVn6VwA3V7VfO2jSO4t6FXyVJkH0SxmwH9d8U7l4p3N6Yz1K',
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
