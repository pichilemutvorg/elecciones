<?php

namespace App\Services;

use App\Models\Mesa;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Sleep;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ElectionSimulator
{
    protected const ELECTORAL_UNIVERSE = 400;

    protected const MIN_PARTICIPATION = 80;

    protected const MAX_PARTICIPATION = 100;

    protected const NULL_VOTES_MIN = 1;

    protected const NULL_VOTES_MAX = 5;

    protected const BLANK_VOTES_MIN = 1;

    protected const BLANK_VOTES_MAX = 4;

    protected const MAX_VOTES_PER_CANDIDATE = 180;

    protected Collection $mesas;

    protected Collection $candidates;

    protected Model $blankCandidate;

    protected Model $nullCandidate;

    protected ?ProgressBar $progressBar = null;

    public function __construct(
        protected readonly OutputInterface $output,
        protected readonly int $interval = 7
    ) {
        $this->mesas = Mesa::inRandomOrder()->get();
    }

    abstract protected function getCandidates(): Collection;

    abstract protected function getBlankCandidate(): Model;

    abstract protected function getNullCandidate(): Model;

    abstract protected function createResult(int $mesaId, int $candidateId, int $votes): void;

    public function simulate(): void
    {
        $this->initializeSimulation();
        $this->runSimulation();
        $this->finalizeSimulation();
    }

    protected function initializeSimulation(): void
    {
        $this->candidates = $this->getCandidates();
        $this->blankCandidate = $this->getBlankCandidate();
        $this->nullCandidate = $this->getNullCandidate();
        $this->progressBar = $this->output->createProgressBar(count($this->mesas));
        $this->progressBar->start();
    }

    protected function runSimulation(): void
    {
        foreach ($this->mesas as $mesa) {
            $this->processTable($mesa);
            $this->progressBar->advance();
            Sleep::for($this->interval)->seconds();
        }
    }

    protected function finalizeSimulation(): void
    {
        $this->progressBar->finish();
    }

    protected function processTable(Mesa $mesa): void
    {
        // Calculate total votes and participation
        $participationRate = rand(self::MIN_PARTICIPATION, self::MAX_PARTICIPATION) / 100;
        $totalVotes = (int) (self::ELECTORAL_UNIVERSE * $participationRate);

        // Calculate null and blank votes
        $nullVotes = (int) ($totalVotes * rand(self::NULL_VOTES_MIN, self::NULL_VOTES_MAX) / 100);
        $blankVotes = (int) ($totalVotes * rand(self::BLANK_VOTES_MIN, self::BLANK_VOTES_MAX) / 100);
        $remainingVotes = $totalVotes - $nullVotes - $blankVotes;

        // Create null and blank votes results
        $this->createResult($mesa->id, $this->nullCandidate->id, $nullVotes);
        $this->createResult($mesa->id, $this->blankCandidate->id, $blankVotes);

        // Distribute remaining votes among candidates
        $votesPerCandidate = $this->distributeVotes($remainingVotes);

        // Create results for each candidate
        foreach ($votesPerCandidate as $candidateId => $votes) {
            $this->createResult($mesa->id, $candidateId, $votes);
        }
    }

    protected function distributeVotes(int $remainingVotes): array
    {
        $votesPerCandidate = array_fill_keys($this->candidates->pluck('id')->toArray(), 0);
        $votesDistributed = 0;

        while ($votesDistributed < $remainingVotes) {
            $candidateId = array_rand($votesPerCandidate);
            $votesToAdd = min(
                rand(1, self::MAX_VOTES_PER_CANDIDATE),
                $remainingVotes - $votesDistributed
            );
            $votesPerCandidate[$candidateId] += $votesToAdd;
            $votesDistributed += $votesToAdd;
        }

        return $votesPerCandidate;
    }
}
