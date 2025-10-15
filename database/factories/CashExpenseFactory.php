<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CashExpense>
 */
class CashExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'recorded_by' => User::factory(),
            'description' => fake()->sentence(),
            'amount' => fake()->numberBetween(5000, 40000),
            'date' => fake()->dateTimeBetween('-2 months', 'now')->format('Y-m-d'),
            'status' => 'confirmed',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }
}
