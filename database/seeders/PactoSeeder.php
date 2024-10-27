<?php

namespace Database\Seeders;

use App\Models\Pacto;
use Illuminate\Database\Seeder;

class PactoSeeder extends Seeder
{
    public function run(): void
    {
        $pactos = [
            ['name' => 'Por Chile, Seguimos', 'letter' => 'A'],
            ['name' => 'Izquierda Ecologista Popular', 'letter' => 'B'],
            ['name' => 'Todas y Todos por Chile', 'letter' => 'C'],
            ['name' => 'Demócratas e Independientes', 'letter' => 'D'],
            ['name' => 'Chile Vamos: UDI, Evópoli, Ind.', 'letter' => 'E'],
            ['name' => 'Partido Social Cristiano e Independientes', 'letter' => 'F'],
            ['name' => 'Verdes Liberales por una Comuna Segura', 'letter' => 'G'],
            ['name' => 'Contigo Chile Mejor', 'letter' => 'H'],
            ['name' => 'Chile Vamos: RN, Ind.', 'letter' => 'I'],
            ['name' => 'Amarillos e Independientes', 'letter' => 'J'],
            ['name' => 'Izquierda de Trabajadores e Independientes', 'letter' => 'K'],
            ['name' => 'Tu Región Radical', 'letter' => 'L'],
            ['name' => 'Regiones Verdes Liberales', 'letter' => 'M'],
            ['name' => 'Lo Mejor para Chile', 'letter' => 'N'],
            ['name' => 'Por Chile y sus Regiones', 'letter' => 'O'],
            ['name' => 'Partido de la Gente e Independientes', 'letter' => 'P'],
            ['name' => 'Por un Chile Mejor', 'letter' => 'Q'],
            ['name' => 'Centro Democrático', 'letter' => 'R'],
            ['name' => 'Frente Amplio', 'letter' => 'S'],
            ['name' => 'Tu Comuna Radical', 'letter' => 'T'],
            ['name' => 'Chile Mucho Mejor', 'letter' => 'U'],
            ['name' => 'Chile Vamos: UDI, Independientes', 'letter' => 'V'],
            ['name' => 'Chile Vamos: Evópoli, Independientes', 'letter' => 'W'],
            ['name' => 'Ecologistas, Animalistas e Independientes', 'letter' => 'X'],
            ['name' => 'Republicanos e Independientes', 'letter' => 'Y'],
            ['name' => 'Chile Vamos', 'letter' => 'Z'],
        ];

        foreach ($pactos as $pactoData) {
            Pacto::create($pactoData);
        }
    }
}
