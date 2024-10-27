<?php

namespace Database\Seeders;

use App\Models\Local;
use Illuminate\Database\Seeder;

class LocalSeeder extends Seeder
{
    public function run(): void
    {
        $locales = [
            ['name' => 'Escuela Digna Camilo Aguilar', 'address' => 'José Joaquín Pérez 261'],
            ['name' => 'Liceo Agustín Ross Edwards', 'address' => 'Ángel Gaete 725'],
            ['name' => 'Colegio Divino Maestro', 'address' => 'Los Jazmines 1365'],
        ];

        foreach ($locales as $localData) {
            Local::create($localData);
        }
    }
}
