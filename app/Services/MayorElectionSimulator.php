<?php

namespace App\Services;

use App\Models\Alcalde;
use App\Models\ResultadosAlcalde;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class MayorElectionSimulator extends ElectionSimulator
{
    protected function getCandidates(): Collection
    {
        return Alcalde::whereNotIn('name', ['Blancos', 'Nulos'])->get();
    }

    protected function getBlankCandidate(): Model
    {
        return Alcalde::where('name', 'Blancos')->firstOrFail();
    }

    protected function getNullCandidate(): Model
    {
        return Alcalde::where('name', 'Nulos')->firstOrFail();
    }

    protected function createResult(int $mesaId, int $candidateId, int $votes): void
    {
        ResultadosAlcalde::factory()->create([
            'mesa_id' => $mesaId,
            'alcalde_id' => $candidateId,
            'votes' => $votes,
        ]);
    }
}
