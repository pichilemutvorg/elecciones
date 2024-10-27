<?php

namespace App\Services;

use App\Models\Concejal;
use App\Models\ResultadosConcejal;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CouncilorElectionSimulator extends ElectionSimulator
{
    protected function getCandidates(): Collection
    {
        return Concejal::whereNotIn('name', ['Blancos', 'Nulos'])->get();
    }

    protected function getBlankCandidate(): Model
    {
        return Concejal::where('name', 'Blancos')->firstOrFail();
    }

    protected function getNullCandidate(): Model
    {
        return Concejal::where('name', 'Nulos')->firstOrFail();
    }

    protected function createResult(int $mesaId, int $candidateId, int $votes): void
    {
        ResultadosConcejal::factory()->create([
            'mesa_id' => $mesaId,
            'concejal_id' => $candidateId,
            'votes' => $votes,
        ]);
    }
}
