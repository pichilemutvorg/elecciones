<?php

namespace Database\Seeders;

use App\Models\Alcalde;
use App\Models\Pacto;
use App\Models\Partido;
use Illuminate\Database\Seeder;

class AlcaldeSeeder extends Seeder
{
    public function run(): void
    {
        Alcalde::create([
            'number' => 50,
            'name' => 'Cristian Pozo Parraguez',
            'color' => '#87c211',
            'pacto_id' => Pacto::where('name', 'Contigo Chile Mejor')->first()->id,
            'partido_id' => Partido::where('name', 'Partido Socialista de Chile')->first()->id,
            'is_independent' => false,
        ]);

        Alcalde::create([
            'number' => 51,
            'name' => 'Tobías Acuña Csillag',
            'color' => '#0e8586',
            'pacto_id' => Pacto::where('name', 'Chile Vamos')->first()->id,
            'partido_id' => null,
            'is_independent' => true,
        ]);

        Alcalde::create([
            'number' => 52,
            'name' => 'Roberto Córdova Carreño',
            'color' => '#e85a36',
            'pacto_id' => null,
            'partido_id' => null,
            'is_independent' => true,
        ]);

        Alcalde::create([
            'number' => 53,
            'name' => 'Jorge Urzúa García',
            'color' => '#5a00ba',
            'pacto_id' => null,
            'partido_id' => null,
            'is_independent' => true,
        ]);

        Alcalde::create([
            'number' => 998,
            'name' => 'Blancos',
            'color' => '#e5e7eb',
            'pacto_id' => null,
            'partido_id' => null,
            'is_independent' => null,
        ]);

        Alcalde::create([
            'number' => 999,
            'name' => 'Nulos',
            'color' => '#71717a',
            'pacto_id' => null,
            'partido_id' => null,
            'is_independent' => null,
        ]);
    }
}
