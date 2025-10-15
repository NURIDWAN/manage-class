<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->sentence(8),
            'date' => fake()->dateTimeBetween('-1 month', '+2 months')->format('Y-m-d'),
            'created_by' => User::factory(),
        ];
    }
}
