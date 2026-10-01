<?php

namespace Database\Factories;

use App\Models\Subpacto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subpacto>
 */
class SubpactoFactory extends Factory
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

        ];
    }
}
