<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            LocalSeeder::class,
            MesaSeeder::class,
            PactoSeeder::class,
            SubpactoSeeder::class,
            PartidoSeeder::class,
            AlcaldeSeeder::class,
            ConcejalSeeder::class,
        ]);
    }
}
