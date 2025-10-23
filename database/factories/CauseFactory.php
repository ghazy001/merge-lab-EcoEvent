<?php

namespace Database\Factories;

use App\Models\Cause;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cause>
 */
class CauseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Cause::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'goal_amount' => $this->faker->randomFloat(2, 1000, 100000), // Random goal between 1,000 and 100,000
            'status' => $this->faker->randomElement(['active', 'completed', 'canceled']),

        ];
    }
}
