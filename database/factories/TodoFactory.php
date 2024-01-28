<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $due_date = fake()->randomElement([null, fake()->dateTimeBetween("+5 days", "+30 days")]);
        return [
            'description' => fake()->realTextBetween(50, 100),
            'completed' => false,
            'priority' => fake()->boolean(),
            'due_date' => $due_date,
        ];
    }
}
