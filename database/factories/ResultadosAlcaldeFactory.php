<?php

namespace Database\Factories;

use App\Models\Alcalde;
use App\Models\Mesa;
use App\Models\ResultadosAlcalde;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResultadosAlcalde>
 */
class ResultadosAlcaldeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mesa_id' => Mesa::inRandomOrder()->first()->id,
            'alcalde_id' => Alcalde::inRandomOrder()->first()->id,
            'votes' => $this->faker->numberBetween(0, 100),
        ];
    }
}
