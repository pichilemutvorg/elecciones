<?php

use function Livewire\Volt\{state, computed, mount};

state([
    'activeTab' => 'alcalde',
    'alcaldesResults' => [],
    'concejalesResults' => [],
    'nextRefresh' => 5,
    'isComplete' => false,
    'electedCouncilors' => []
]);

mount(function () {
    $this->loadResults();
});

$calculateDHondt = function ($results, $seats = 6) {
    // 1. Primer cálculo D'Hondt a nivel de pacto
    $pactos = collect($results)
        ->filter(fn($r) => !in_array($r['name'], ['Blancos', 'Nulos']))
        ->groupBy('pacto')
        ->map(function ($candidates) {
            $votes = $candidates->sum('votes');
            return [
                'votes' => $votes,
                'candidates' => $candidates,
                // Agrupar por subpacto dentro del pacto
                'subpactos' => $candidates->groupBy('subpacto')
                    ->map(fn($subCandidates) => [
                        'votes' => $subCandidates->sum('votes'),
                        'candidates' => $subCandidates->sortByDesc('votes')->values()
                    ])
            ];
        })
        ->filter(fn($pacto) => $pacto['votes'] > 0);

    // Calcular cocientes para pactos
    $quotients = collect();
    foreach ($pactos as $pactoName => $pacto) {
        for ($i = 1; $i <= $seats; $i++) {
            $quotients->push([
                'pacto' => $pactoName,
                'quotient' => floor($pacto['votes'] / $i),
                'divisor' => $i,
                'subpactos' => $pacto['subpactos']
            ]);
        }
    }

    // Determinar escaños por pacto
    $winners = $quotients->sortByDesc('quotient')
        ->take($seats);

    $seatsPerPacto = $winners->groupBy('pacto')
        ->map(fn($group) => $group->count());

    // 2. Segundo cálculo D'Hondt dentro de cada pacto para sus subpactos
    $elected = collect();
    foreach ($seatsPerPacto as $pactoName => $pactoSeats) {
        $pactoData = $pactos[$pactoName];
        $subpactos = $pactoData['subpactos'];

        // Si el pacto solo tiene un subpacto, asignar directamente
        if ($subpactos->count() === 1) {
            $subpacto = $subpactos->first();
            $elected = $elected->merge(
                $subpacto['candidates']
                    ->take($pactoSeats)
                    ->pluck('name')
            );
            continue;
        }

        // Calcular cocientes para subpactos
        $subQuotients = collect();
        foreach ($subpactos as $subpactoName => $subpacto) {
            for ($i = 1; $i <= $pactoSeats; $i++) {
                $subQuotients->push([
                    'subpacto' => $subpactoName,
                    'quotient' => floor($subpacto['votes'] / $i),
                    'candidates' => $subpacto['candidates']
                ]);
            }
        }

        // Asignar escaños a subpactos
        $subpactoWinners = $subQuotients->sortByDesc('quotient')
            ->take($pactoSeats)
            ->groupBy('subpacto');

        // Seleccionar candidatos de cada subpacto
        foreach ($subpactoWinners as $subpactoName => $wins) {
            $seatsForSubpacto = $wins->count();
            $candidates = $subpactos[$subpactoName]['candidates']
                ->take($seatsForSubpacto)
                ->pluck('name');
            $elected = $elected->merge($candidates);
        }
    }

    return $elected->toArray();
};

$loadResults = function () {
    $this->alcaldesResults = \App\Models\Alcalde::query()
        ->withSum('votacion', 'votes')
        ->with(['partido', 'pacto'])
        ->orderBy('number')
        ->get()
        ->map(function ($alcalde) {
            $totalVotes = \App\Models\ResultadosAlcalde::sum('votes');
            $percentage = $totalVotes > 0
                ? number_format(($alcalde->votacion_sum_votes / $totalVotes) * 100, 1)
                : '0.0';

            return [
                'number' => $alcalde->number,
                'name' => $alcalde->name,
                'votes' => $alcalde->votacion_sum_votes ?? 0,
                'percentage' => $percentage,
                'color' => $alcalde->color,
                'photo' => $alcalde->photo
                    ? \Storage::disk('public')->url($alcalde->photo)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($alcalde->name),
                'partido' => $alcalde->is_independent ? 'IND' : ($alcalde->partido?->abbr ?? ''),
                'pacto' => $alcalde->pacto?->name
            ];
        });

    // Cargar y procesar resultados de concejales
    $concejales = \App\Models\Concejal::query()
        ->withSum('votacion', 'votes')
        ->with(['partido', 'pacto', 'subpacto'])
        ->orderBy('number')
        ->get()
        ->map(function ($concejal) {
            $totalVotes = \App\Models\ResultadosConcejal::sum('votes');
            $percentage = $totalVotes > 0
                ? number_format(($concejal->votacion_sum_votes / $totalVotes) * 100, 1)
                : '0.0';

            return [
                'number' => $concejal->number,
                'name' => $concejal->name,
                'votes' => $concejal->votacion_sum_votes ?? 0,
                'percentage' => $percentage,
                'color' => $concejal->color,
                'photo' => $concejal->photo
                    ? \Storage::disk('public')->url($concejal->photo)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($concejal->name),
                'partido' => $concejal->is_independent ? 'IND' : ($concejal->partido?->abbr ?? ''),
                'pacto' => $concejal->pacto?->name,
                'subpacto' => $concejal->subpacto?->name
            ];
        });

    $this->concejalesResults = $concejales;

    // Calcular electos si el escrutinio está avanzado
    if ($this->activeTab === 'concejal') {
        $progress = $this->getProgress();
        if ($progress >= 95) {
            $this->electedCouncilors = $this->calculateDHondt($this->concejalesResults);
        } else {
            $this->electedCouncilors = [];
        }
    }

    $this->resetTimer();
};

$resetTimer = function () {
    $this->nextRefresh = 5;
};

$setActiveTab = function ($tab) {
    $this->activeTab = $tab;
    $this->loadResults();
};

$getProgress = function () {
    if ($this->activeTab === 'alcalde') {
        $countedMesa = \App\Models\ResultadosAlcalde::distinct('mesa_id')->count();
    } else {
        $countedMesa = \App\Models\ResultadosConcejal::distinct('mesa_id')->count();
    }

    $allMesas = \App\Models\Mesa::count();
    $progress = ($countedMesa / $allMesas) * 100;

    $this->isComplete = $progress >= 100;

    return number_format($progress, 1);
};

?>

<div
    class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50"
    wire:poll.5000ms="loadResults"
>
    <div class="container mx-auto py-4 sm:py-8 px-2 sm:px-4">
        <!-- Header más compacto en móviles -->
        <div class="mb-4 sm:mb-8">
            <h1 class="text-2xl sm:text-4xl font-bold text-blue-900">Resultados Electorales</h1>
            <p class="mt-1 sm:mt-2 text-sm sm:text-base text-blue-600">Seguimiento en tiempo real del escrutinio</p>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-3 sm:p-6 border border-blue-100">
            <!-- Tabs y estado más adaptables -->
            <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row gap-4 sm:gap-0 sm:justify-between sm:items-center">
                <nav class="flex space-x-1" aria-label="Tabs">
                    <button
                        wire:click="setActiveTab('alcalde')"
                        class="{{ $activeTab === 'alcalde'
                            ? 'bg-blue-100 text-blue-700'
                            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}
                            px-3 sm:px-4 py-2 font-medium text-sm rounded-md transition-colors duration-200"
                    >
                        Alcalde
                    </button>
                    <button
                        wire:click="setActiveTab('concejal')"
                        class="{{ $activeTab === 'concejal'
                            ? 'bg-blue-100 text-blue-700'
                            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}
                            px-3 sm:px-4 py-2 font-medium text-sm rounded-md transition-colors duration-200"
                    >
                        Concejales
                    </button>
                </nav>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500">Mesas escrutadas:</span>
                        <span class="font-medium text-blue-700">{{ $this->getProgress() }}%</span>
                    </div>

                    @if(!$isComplete)
                        <div class="flex items-center gap-2 text-gray-500">
                            <svg class="animate-spin h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="hidden sm:inline">Próxima actualización:</span>
                            <span class="font-medium text-blue-600">
                                <span x-data="{ timer: 5 }"
                                      x-init="setInterval(() => {
                                          if (timer > 0) timer--;
                                          if (timer === 0) timer = 5;
                                      }, 1000)"
                                      x-text="timer">5</span>s
                            </span>
                        </div>
                    @else
                        <div class="flex items-center gap-2 text-green-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="font-medium">Escrutinio completado</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tabla responsiva -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-blue-200">
                    <thead>
                    <tr>
                        <th scope="col"
                            class="py-3 pl-2 sm:pl-4 pr-3 text-left text-xs sm:text-sm font-semibold text-blue-900">
                            Candidato
                        </th>
                        <th scope="col"
                            class="px-2 sm:px-3 py-3 text-right text-xs sm:text-sm font-semibold text-blue-900">Votos
                        </th>
                        <th scope="col"
                            class="px-2 sm:px-3 py-3 text-right text-xs sm:text-sm font-semibold text-blue-900">%
                        </th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-100">
                    @foreach($activeTab === 'alcalde' ? $alcaldesResults : $concejalesResults as $result)
                        <tr class="hover:bg-blue-50 transition-colors duration-150">
                            <td class="py-3 sm:py-4 pl-2 sm:pl-4 pr-2 sm:pr-3 text-xs sm:text-sm">
                                <div class="flex flex-col items-start sm:flex-row sm:items-center gap-2 sm:gap-4">
                                    <!-- Foto y nombre en columna para móviles -->
                                    <div class="flex flex-col items-center sm:flex-row gap-1 sm:gap-4 w-full sm:w-auto">
                                        <div class="relative">
                                            <img
                                                src="{{ $result['photo'] }}"
                                                alt="{{ $result['name'] }}"
                                                class="h-12 w-12 sm:h-10 sm:w-10 rounded-full object-cover ring-2 ring-white"
                                                style="border: 2px solid {{ $result['color'] ?? '#e5e7eb' }}"
                                            >
                                            @if(in_array($result['name'], ['Blancos', 'Nulos']))
                                                <span
                                                    class="absolute -top-1 -right-1 h-3 w-3 sm:h-4 sm:w-4 rounded-full bg-gray-400"></span>
                                            @elseif($activeTab === 'concejal' && in_array($result['name'], $electedCouncilors))
                                                <span
                                                    class="absolute -top-1 -right-1 h-3 w-3 sm:h-4 sm:w-4 rounded-full bg-green-500 animate-pulse"></span>
                                            @elseif($result['percentage'] >= 30)
                                                <span
                                                    class="absolute -top-1 -right-1 h-3 w-3 sm:h-4 sm:w-4 rounded-full bg-yellow-400 animate-pulse"></span>
                                            @endif
                                        </div>
                                        <div class="flex flex-col items-center sm:items-start">
                                            <div
                                                class="font-medium text-blue-900 flex items-center gap-2 flex-wrap text-center sm:text-left">
                                                <span class="break-words">
                                                    @if(!in_array($result['name'], ['Blancos', 'Nulos']))
                                                        {{ $result['number'] }}.
                                                    @endif
                                                    {{ $result['name'] }}
                                                </span>
                                                @if($activeTab === 'concejal' && in_array($result['name'], $electedCouncilors))
                                                    <span
                                                        class="inline-flex items-center rounded-md bg-green-50 px-1.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                                        Electo
                                                    </span>
                                                @endif
                                            </div>
                                            @if(!in_array($result['name'], ['Blancos', 'Nulos']))
                                                <div class="text-xs text-gray-500 mt-0.5 text-center sm:text-left">
                                                    <!-- Móvil: solo letra del pacto -->
                                                    <div class="sm:hidden">
                                                        {{ $result['partido'] }}
                                                        @if($result['pacto'])
                                                            • {{ substr($result['pacto'], 0, 1) }}
                                                        @endif
                                                    </div>
                                                    <!-- Desktop: información completa -->
                                                    <div class="hidden sm:flex flex-wrap gap-x-1">
                                                        <span>{{ $result['pacto'] }}</span>
                                                        @if($result['partido'])
                                                            <span>•</span>
                                                            <span>{{ $result['partido'] }}</span>
                                                        @endif
                                                        @if($activeTab === 'concejal' && $result['subpacto'])
                                                            <span>•</span>
                                                            <span>{{ $result['subpacto'] }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 sm:px-3 py-3 sm:py-4 text-right text-xs sm:text-sm">
                                <span
                                    class="font-medium text-blue-900">{{ number_format($result['votes'], 0, ',', '.') }}</span>
                                <span class="text-gray-500 hidden sm:inline">votos</span>
                            </td>
                            <td class="px-2 sm:px-3 py-3 sm:py-4 text-right text-xs sm:text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <div class="w-12 sm:w-20 bg-gray-200 rounded-full h-1.5 sm:h-2">
                                        <div
                                            class="bg-blue-600 h-1.5 sm:h-2 rounded-full transition-all duration-500"
                                            style="width: {{ $result['percentage'] }}%"
                                        ></div>
                                    </div>
                                    <span class="font-medium text-blue-900">{{ $result['percentage'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
