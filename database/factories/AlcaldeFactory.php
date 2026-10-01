<?php

namespace Database\Factories;

use App\Models\Alcalde;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alcalde>
 */
class AlcaldeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => fake()->randomNumber(),
            'name' => fake()->text(50),

        ];
    }
}
