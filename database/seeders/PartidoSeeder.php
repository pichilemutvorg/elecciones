<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Partido;

class PartidoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partidos = [
            ['name' => 'Partido Igualdad', 'abbr' => 'PI'],
            ['name' => 'Partido Humanista de Chile', 'abbr' => 'PH'],
            ['name' => 'Partido Popular', 'abbr' => 'PP'],
            ['name' => 'Partido Social Cristiano', 'abbr' => 'PSC'],
            ['name' => 'Frente Amplio', 'abbr' => 'FA'],
            ['name' => 'Partido Comunista de Chile', 'abbr' => 'PC'],
            ['name' => 'Acción Humanista', 'abbr' => 'AH'],
            ['name' => 'Federación Regionalista Verde Social', 'abbr' => 'FRVS'],
            ['name' => 'Partido Liberal de Chile', 'abbr' => 'PL'],
            ['name' => 'Partido Radical de Chile', 'abbr' => 'PR'],
            ['name' => 'Partido Socialista de Chile', 'abbr' => 'PS'],
            ['name' => 'Partido por la Democracia', 'abbr' => 'PPD'],
            ['name' => 'Partido Demócrata Cristiano', 'abbr' => 'PDC'],
            ['name' => 'Partido de Trabajadores Revolucionarios', 'abbr' => 'PTR'],
            ['name' => 'Partido de la Gente', 'abbr' => 'PDG'],
            ['name' => 'Amarillos por Chile', 'abbr' => 'APC'],
            ['name' => 'Demócratas', 'abbr' => 'DEM'],
            ['name' => 'Partido Alianza Verde Popular', 'abbr' => 'PAVP'],
            ['name' => 'Partido Republicano de Chile', 'abbr' => 'PLR'],
            ['name' => 'Unión Demócrata Independiente', 'abbr' => 'UDI'],
            ['name' => 'Evolución Política', 'abbr' => 'EVOP'],
            ['name' => 'Renovación Nacional', 'abbr' => 'RN'],
        ];

        foreach ($partidos as $partidoData) {
            Partido::create($partidoData);
        }
    }
}
