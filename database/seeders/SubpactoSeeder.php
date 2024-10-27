<?php

namespace Database\Seeders;

use App\Models\Subpacto;
use Illuminate\Database\Seeder;

class SubpactoSeeder extends Seeder
{
    public function run(): void
    {
        $subpactos = [
            // Pacto Por Chile, seguimos
            'Frente Amplio e Independientes',

            // Pacto Chile Vamos UDI-Evópoli
            'UDI e Independientes',

            // Pacto Verdes Liberales
            'Partido Federación Regionalista Verde Social e Independientes',

            // Pacto Chile Vamos RN
            'Renovación Nacional e Independientes',

            // Pacto Centro Democrático
            'Amarillos e Independientes',

            // Pacto Chile mucho mejor
            'PS e Independientes',
            'PDC e Independientes',

            // Pacto Republicanos
            'Republicanos e Independientes',
        ];

        foreach ($subpactos as $name) {
            Subpacto::create([
                'name' => $name,
            ]);
        }
    }
}
