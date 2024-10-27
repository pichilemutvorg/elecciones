<?php

namespace Database\Seeders;

use App\Models\Concejal;
use App\Models\Pacto;
use App\Models\Partido;
use App\Models\Subpacto;
use Illuminate\Database\Seeder;

class ConcejalSeeder extends Seeder
{
    public function run(): void
    {
        // Pacto Por Chile, seguimos (A)
        $pactoChile = Pacto::where('letter', 'A')->first();
        $subpactoFA = Subpacto::where('name', 'Frente Amplio e Independientes')->first();

        // Independientes del Frente Amplio
        Concejal::create([
            'number' => 250,
            'name' => 'Lysette Toro Guzmán',
            'is_independent' => true,
            'pacto_id' => $pactoChile->id,
            'subpacto_id' => $subpactoFA->id,
        ]);

        Concejal::create([
            'number' => 251,
            'name' => 'María Paz Miranda',
            'is_independent' => true,
            'pacto_id' => $pactoChile->id,
            'subpacto_id' => $subpactoFA->id,
        ]);

        Concejal::create([
            'number' => 252,
            'name' => 'Pablo Canales Saravia',
            'is_independent' => true,
            'pacto_id' => $pactoChile->id,
            'subpacto_id' => $subpactoFA->id,
        ]);

        // Pacto Chile Vamos UDI-Evópoli (E)
        $pactoUDI = Pacto::where('letter', 'E')->first();
        $subpactoUDI = Subpacto::where('name', 'UDI e Independientes')->first();
        $partidoUDI = Partido::where('abbr', 'UDI')->first();

        // Candidatos UDI
        Concejal::create([
            'number' => 253,
            'name' => 'Daniela Toro Canceco',
            'is_independent' => false,
            'partido_id' => $partidoUDI->id,
            'pacto_id' => $pactoUDI->id,
            'subpacto_id' => $subpactoUDI->id,
        ]);

        Concejal::create([
            'number' => 254,
            'name' => 'Luis Pavez Vargas',
            'is_independent' => false,
            'partido_id' => $partidoUDI->id,
            'pacto_id' => $pactoUDI->id,
            'subpacto_id' => $subpactoUDI->id,
        ]);

        // Independientes UDI
        foreach ([
            255 => 'Aníbal Galarce Sandoval',
            256 => 'Katherine Guardia Pérez',
            257 => 'Hernán Pelegri Ahumada',
            258 => 'Roberto Cuadra Aránguiz',
        ] as $number => $name) {
            Concejal::create([
                'number' => $number,
                'name' => $name,
                'is_independent' => true,
                'pacto_id' => $pactoUDI->id,
                'subpacto_id' => $subpactoUDI->id,
            ]);
        }

        // Pacto Verdes Liberales (G)
        $pactoVerdes = Pacto::where('letter', 'G')->first();
        $subpactoFRVS = Subpacto::where('name', 'Partido Federación Regionalista Verde Social e Independientes')->first();

        // Independientes Verdes
        foreach ([
            259 => 'Alonso Vargas Valencia',
            260 => 'Luis Hernán Polanco',
            261 => 'Víctor Rojas González',
            262 => 'Magaly Vargas Flores',
            263 => 'Jorge Daniel Vargas',
            264 => 'Álvaro Álvarez Pérez',
        ] as $number => $name) {
            Concejal::create([
                'number' => $number,
                'name' => $name,
                'is_independent' => true,
                'pacto_id' => $pactoVerdes->id,
                'subpacto_id' => $subpactoFRVS->id,
            ]);
        }

        // Pacto Chile Vamos RN (I)
        $pactoRN = Pacto::where('letter', 'I')->first();
        $subpactoRN = Subpacto::where('name', 'Renovación Nacional e Independientes')->first();
        $partidoRN = Partido::where('abbr', 'RN')->first();

        // Candidatos RN
        foreach ([
            265 => 'Danilo Barrera Cáceres',
            266 => 'Sofía Yávar Ramírez',
        ] as $number => $name) {
            Concejal::create([
                'number' => $number,
                'name' => $name,
                'is_independent' => false,
                'partido_id' => $partidoRN->id,
                'pacto_id' => $pactoRN->id,
                'subpacto_id' => $subpactoRN->id,
            ]);
        }

        // Independientes RN
        foreach ([
            267 => 'Tatiana Cornejo Jorquera',
            268 => 'Ricardo Jofré Arriagada',
            269 => 'Marcelo Cabrera Martínez',
        ] as $number => $name) {
            Concejal::create([
                'number' => $number,
                'name' => $name,
                'is_independent' => true,
                'pacto_id' => $pactoRN->id,
                'subpacto_id' => $subpactoRN->id,
            ]);
        }

        // Pacto Centro Democrático (R)
        $pactoCentro = Pacto::where('letter', 'R')->first();
        $subpactoAmarillos = Subpacto::where('name', 'Amarillos e Independientes')->first();

        Concejal::create([
            'number' => 270,
            'name' => 'Carolina Azócar Jelincic',
            'is_independent' => true,
            'pacto_id' => $pactoCentro->id,
            'subpacto_id' => $subpactoAmarillos->id,
        ]);

        // Pacto Chile mucho mejor (U)
        $pactoMejor = Pacto::where('letter', 'U')->first();
        $subpactoPS = Subpacto::where('name', 'PS e Independientes')->first();
        $subpactoPDC = Subpacto::where('name', 'PDC e Independientes')->first();
        $partidoPS = Partido::where('abbr', 'PS')->first();
        $partidoPDC = Partido::where('abbr', 'PDC')->first();

        // Candidatos PS
        foreach ([
            271 => 'Verónica Ramírez Tapia',
            272 => 'Denis Lizana Soto',
        ] as $number => $name) {
            Concejal::create([
                'number' => $number,
                'name' => $name,
                'is_independent' => false,
                'partido_id' => $partidoPS->id,
                'pacto_id' => $pactoMejor->id,
                'subpacto_id' => $subpactoPS->id,
            ]);
        }

        // Independiente PS
        Concejal::create([
            'number' => 273,
            'name' => 'José Luis Cabrera',
            'is_independent' => true,
            'pacto_id' => $pactoMejor->id,
            'subpacto_id' => $subpactoPS->id,
        ]);

        // Candidato PDC
        Concejal::create([
            'number' => 274,
            'name' => 'Raúl Tobar Pavez',
            'is_independent' => false,
            'partido_id' => $partidoPDC->id,
            'pacto_id' => $pactoMejor->id,
            'subpacto_id' => $subpactoPDC->id,
        ]);

        // Independientes PDC
        foreach ([
            275 => 'Danilo Robles Cáceres',
            276 => 'Estrella Alarcón Rojas',
        ] as $number => $name) {
            Concejal::create([
                'number' => $number,
                'name' => $name,
                'is_independent' => true,
                'pacto_id' => $pactoMejor->id,
                'subpacto_id' => $subpactoPDC->id,
            ]);
        }

        // Pacto Republicanos (Y)
        $pactoRepublicano = Pacto::where('letter', 'Y')->first();
        $subpactoRepublicano = Subpacto::where('name', 'Republicanos e Independientes')->first();
        $partidoRepublicano = Partido::where('abbr', 'PLR')->first();

        // Candidato Republicano
        Concejal::create([
            'number' => 277,
            'name' => 'Víctor Concha Lara',
            'is_independent' => false,
            'partido_id' => $partidoRepublicano->id,
            'pacto_id' => $pactoRepublicano->id,
            'subpacto_id' => $subpactoRepublicano->id,
        ]);

        // Independientes Republicanos
        foreach ([
            278 => 'Valeria Peñaloza Ahumada',
            279 => 'Giovanna Valencia Araya',
        ] as $number => $name) {
            Concejal::create([
                'number' => $number,
                'name' => $name,
                'is_independent' => true,
                'pacto_id' => $pactoRepublicano->id,
                'subpacto_id' => $subpactoRepublicano->id,
            ]);
        }

        // Votos Blancos y Nulos
        Concejal::create([
            'number' => 998,
            'name' => 'Blancos',
            'color' => '#e5e7eb',
            'is_independent' => null,
        ]);

        Concejal::create([
            'number' => 999,
            'name' => 'Nulos',
            'color' => '#71717a',
            'is_independent' => null,
        ]);
    }
}
