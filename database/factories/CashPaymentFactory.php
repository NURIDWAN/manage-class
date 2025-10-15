<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CashPayment>
 */
class CashPaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'amount' => fake()->numberBetween(5000, 50000),
            'date' => fake()->dateTimeBetween('-2 months', 'now')->format('Y-m-d'),
            'status' => fake()->randomElement(['pending', 'confirmed']),
            'payment_method' => fake()->randomElement(['cash', 'transfer']),
            'proof_path' => null,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn () => ['status' => 'confirmed']);
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }
}
