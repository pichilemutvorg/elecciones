<?php

namespace Tests\Feature;

use App\Models\Alcalde;
use App\Models\Concejal;
use App\Models\Mesa;
use App\Models\ResultadosAlcalde;
use App\Models\ResultadosConcejal;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ElectionSimulationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_it_simulates_a_full_election(): void
    {
        $mesas = $this->runSimulation();

        $this->assertSame(49, $mesas);
        $this->assertSame(0, ResultadosAlcalde::query()->where('votes', '<', 0)->count());
        $this->assertSame(0, ResultadosConcejal::query()->where('votes', '<', 0)->count());
    }

    public function test_every_mesa_reports_results_for_the_mayor_election(): void
    {
        $mesas = $this->runSimulation();

        $mesasWithMayorResults = ResultadosAlcalde::query()
            ->distinct('mesa_id')
            ->count('mesa_id');

        $this->assertSame($mesas, $mesasWithMayorResults);
    }

    public function test_every_mesa_reports_results_for_the_councilor_election(): void
    {
        $mesas = $this->runSimulation();

        $mesasWithCouncilorResults = ResultadosConcejal::query()
            ->distinct('mesa_id')
            ->count('mesa_id');

        $this->assertSame($mesas, $mesasWithCouncilorResults);
    }

    public function test_votes_per_mesa_never_exceed_the_electoral_universe(): void
    {
        $this->runSimulation();

        // ElectionSimulator::ELECTORAL_UNIVERSE
        $electoralUniverse = 400;

        foreach (Mesa::pluck('id') as $mesaId) {
            $mayorVotes = ResultadosAlcalde::where('mesa_id', $mesaId)->sum('votes');
            $this->assertLessThanOrEqual(
                $electoralUniverse,
                $mayorVotes,
                "La suma de votos para la mesa {$mesaId} excede el universo electoral."
            );
        }
    }

    public function test_blank_and_null_candidates_always_receive_votes(): void
    {
        $this->runSimulation();

        foreach (['Blancos', 'Nulos'] as $name) {
            $this->assertTrue(
                Alcalde::where('name', $name)->whereHas('votacion')->exists(),
                "El candidato {$name} no registro votos en ninguna mesa."
            );
        }
    }

    public function test_councilors_receive_votes(): void
    {
        $this->runSimulation();

        $this->assertGreaterThan(
            0,
            Concejal::query()->whereHas('votacion')->count()
        );
    }

    /**
     * Runs the simulation command and returns the number of mesas.
     */
    protected function runSimulation(): int
    {
        $this->artisan('election:simulate', ['--interval' => 0])
            ->assertSuccessful();

        return Mesa::count();
    }
}
