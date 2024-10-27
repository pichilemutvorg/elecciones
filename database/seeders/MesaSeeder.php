<?php

namespace Database\Seeders;

use App\Models\Local;
use App\Models\Mesa;
use Illuminate\Database\Seeder;

class MesaSeeder extends Seeder
{
    public function run(): void
    {
        $locales = [
            ['name' => 'Escuela Digna Camilo Aguilar', 'mesas' => range(1, 17)],
            ['name' => 'Colegio Divino Maestro', 'mesas' => range(18, 31)],
            ['name' => 'Liceo Agustín Ross Edwards', 'mesas' => range(32, 49)],
        ];

        foreach ($locales as $localData) {
            $local = Local::where('name', $localData['name'])->first();
            foreach ($localData['mesas'] as $numeroMesa) {
                Mesa::create([
                    'local_id' => $local->id,
                    'number' => $numeroMesa,
                ]);
            }
        }
    }
}
