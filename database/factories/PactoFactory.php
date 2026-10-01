<?php

namespace Database\Factories;

use App\Models\Pacto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pacto>
 */
class PactoFactory extends Factory
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
            'icon' => fake()->text(50),

        ];
    }
}
