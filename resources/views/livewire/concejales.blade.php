<?php

use App\Models\Concejal;
use App\Models\Mesa;
use App\Models\ResultadosConcejal;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Number;
use Livewire\Volt\Component;

new class extends Component {
    public Collection $concejales;
    public int $totalVotos;
    public int $totalMesas;
    public int $mesasEscrutadas;
    public string $porcentajeMesas;
    public bool $escrutinioCompleto = false;

    public function mount(): void
    {
        $this->totalMesas = Mesa::count();
        $this->loadData();
    }

    public function refresh(): void
    {
        $this->loadData();
    }

    protected function loadData(): void
    {
        $this->mesasEscrutadas = ResultadosConcejal::distinct('mesa_id')->count('mesa_id');
        $this->porcentajeMesas = $this->totalMesas > 0
            ? Number::percentage($this->mesasEscrutadas / $this->totalMesas * 100, 0)
            : '0%';

        $this->escrutinioCompleto = $this->mesasEscrutadas >= $this->totalMesas;

        $this->totalVotos = ResultadosConcejal::sum('votes');

        $electos = $this->obtenerElectos();

        $this->concejales = Concejal::with('votacion')
            ->whereIn('id', $electos->pluck('id'))
            ->get()
            ->map(function ($concejal) {
                $votosConcejal = $concejal->votacion->sum('votes');
                $concejal->votos_count = $votosConcejal;
                $concejal->photoURL = $concejal->photo
                    ? Storage::disk('public')->url($concejal->photo)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($concejal->name);

                // Generar color consistente basado en la letra del pacto o nombre
                $concejal->color = match (true) {
                    $concejal->pacto?->letter !== null => '#' . substr(md5($concejal->pacto->letter), 0, 6),
                    default => '#' . substr(md5($concejal->name), 0, 6),
                };

                // Calculamos el porcentaje real
                $porcentajeReal = $this->totalVotos > 0
                    ? ($votosConcejal / $this->totalVotos * 100)
                    : 0;

                // Guardamos el porcentaje real para mostrar
                $concejal->porcentaje = Number::percentage($porcentajeReal, 1);

                // Calculamos un porcentaje exagerado para la barra
                $porcentajeExagerado = $porcentajeReal > 0
                    ? pow($porcentajeReal, 1.25)
                    : 0;

                // Normalizamos para que el máximo no exceda el 100%
                $concejal->porcentaje_barra = $porcentajeExagerado . '%';

                return $concejal;
            })
            ->sortByDesc('votos_count')
            ->values();
    }

    protected function obtenerElectos()
    {
        // La misma lógica de tu TablaResultadosConcejales
        // 1. Obtener votos totales por pacto
        $votosPorPacto = Concejal::query()
            ->whereNotNull('pacto_id')
            ->whereNotIn('name', ['Blancos', 'Nulos'])
            ->withSum('votacion', 'votes')
            ->get()
            ->groupBy('pacto_id')
            ->map(function ($concejales) {
                return [
                    'votos_totales' => $concejales->sum('votacion_sum_votes'),
                    'concejales' => $concejales->sortByDesc('votacion_sum_votes'),
                ];
            });

        // 2. Calcular coeficientes D'Hondt
        $divisores = collect();
        foreach ($votosPorPacto as $pactoId => $datos) {
            for ($i = 1; $i <= 6; $i++) {
                $divisores->push([
                    'pacto_id' => $pactoId,
                    'coeficiente' => $datos['votos_totales'] / $i,
                    'votos' => $datos['votos_totales'],
                    'divisor' => $i,
                ]);
            }
        }

        // 3. Obtener los mayores coeficientes
        $asignaciones = $divisores
            ->sortByDesc('coeficiente')
            ->take(6)
            ->groupBy('pacto_id')
            ->map(function ($grupo) {
                return $grupo->count();
            });

        // 4. Seleccionar los candidatos más votados de cada pacto
        $electos = collect();
        foreach ($asignaciones as $pactoId => $numEscanos) {
            $candidatosPacto = $votosPorPacto[$pactoId]['concejales']
                ->take($numEscanos);
            $electos = $electos->concat($candidatosPacto);
        }

        return $electos->sortByDesc('votacion_sum_votes');
    }
}; ?>

<div>
    <main
        {{ !$escrutinioCompleto ? 'wire:poll.1s.keep-alive=refresh' : '' }} class="px-[96px] py-[54px] pb-[196px] h-screen grid grid-cols-3 gap-2">
        <div class="bg-fuchsia-900 text-gray-100 p-8 rounded-lg col-span-2">
            <!-- Header con título y conteo de mesas -->
            <div class="grid grid-cols-3 mb-12">
                <h1 class="text-6xl font-bold col-span-2">Concejales 2024</h1>
                <aside class="text-right">
                    <p class="text-4xl font-bold">{{ $porcentajeMesas }}</p>
                    <p class="uppercase text-fuchsia-200">Mesas escrutadas</p>
                </aside>
            </div>

            <!-- Resultados -->
            <div>
                @foreach($concejales as $concejal)
                    <div class="grid grid-cols-12 gap-x-6 items-center px-4 py-2">
                        <!-- Foto y nombre -->
                        <div class="col-span-10 flex items-center gap-6">
                            <img
                                src="{{ $concejal->photoURL }}"
                                class="rounded-full object-cover border-2 scale-110 w-20 h-20 bg-[{{ $concejal->color }}] border-[{{ $concejal->color }}]"
                                alt="{{ $concejal->name }}"
                            >
                            <div>
                                <h3 class="text-3xl">{{ $concejal->name }}</h3>
                                <p class="text-lg uppercase text-fuchsia-200 mt-2">
                                    {{ $concejal->pacto->name }}
                                    • {{ $concejal->is_independent ? 'Independiente' : $concejal->partido?->abbr }}
                                </p>
                            </div>
                        </div>

                        <!-- Votos -->
                        <div class="col-span-2 text-right text-4xl font-bold">
                            {{ number_format($concejal->votos_count) }}
                            <p class="text-lg text-fuchsia-200">votos válidos</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Footer con última actualización -->
            @if(!$escrutinioCompleto)
                <div class="mt-12 text-center text-fuchsia-200 text-xl">
                    <p>Última actualización: {{ now('America/Santiago')->format('H:i:s') }}</p>
                </div>
            @else
                <div class="mt-12 text-center text-fuchsia-200 text-xl">
                    <p>Resultados preliminares. Vea los resultados oficiales en <strong>servel.cl</strong></p>
                </div>
            @endif
        </div>
    </main>
</div>
