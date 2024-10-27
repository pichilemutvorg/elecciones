<?php

namespace App\Console\Commands;

use App\Services\CouncilorElectionSimulator;
use App\Services\MayorElectionSimulator;
use Illuminate\Console\Command;

class ElectionSimulation extends Command
{
    protected $signature = 'election:simulate
                            {--interval=7 : Intervalo de tiempo entre cada mesa en segundos}';

    protected $description = 'Simula una elección con resultados de las mesas de forma gradual, con un intervalo de tiempo entre cada mesa.';

    public function handle(): void
    {
        $this->info('Reiniciando la base de datos.');
        $this->call('migrate:refresh');
        $this->call('db:seed');

        $interval = (int) $this->option('interval');

        $this->info('Iniciando simulación de elección de alcaldes.');
        $mayorSimulator = new MayorElectionSimulator($this->output, $interval);
        $mayorSimulator->simulate();
        $this->newLine();
        $this->info('Simulación de elección de alcaldes completada.');

        $this->info('Iniciando simulación de elección de concejales.');
        $councilorSimulator = new CouncilorElectionSimulator($this->output, $interval);
        $councilorSimulator->simulate();
        $this->newLine();
        $this->info('Simulación de elección de concejales completada.');
    }
}
