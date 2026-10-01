<?php

namespace Database\Factories;

use App\Models\Local;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Local>
 */
class LocalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->text(50),
            'address' => fake()->text(50),

        ];
    }
}
