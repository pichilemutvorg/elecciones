<?php

namespace Tests;

use Database\Seeders\AlcaldeSeeder;
use Database\Seeders\ConcejalSeeder;
use Database\Seeders\LocalSeeder;
use Database\Seeders\MesaSeeder;
use Database\Seeders\PactoSeeder;
use Database\Seeders\PartidoSeeder;
use Database\Seeders\SubpactoSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Seed the election domain data.
     *
     * Deliberately excludes Database\Seeders\UserSeeder: it creates a single
     * account with a hardcoded password hash, and tests should never depend on
     * a credential baked into the repository. Test users are created with
     * User::factory() instead.
     */
    protected function seedElectionData(): void
    {
        $this->seed([
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
