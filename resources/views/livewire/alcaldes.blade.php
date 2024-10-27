<?php

use App\Models\Alcalde;
use App\Models\Mesa;
use App\Models\ResultadosAlcalde;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Number;
use Livewire\Volt\Component;

new class extends Component {
    public Collection $alcaldes;
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
        $this->mesasEscrutadas = ResultadosAlcalde::distinct('mesa_id')->count('mesa_id');
        $this->porcentajeMesas = $this->totalMesas > 0
            ? Number::percentage($this->mesasEscrutadas / $this->totalMesas * 100, 0)
            : '0%';

        $this->escrutinioCompleto = $this->mesasEscrutadas >= $this->totalMesas;

        $this->totalVotos = ResultadosAlcalde::sum('votes');

        $this->alcaldes = Alcalde::with('votacion')
            ->get()
            ->map(function ($alcalde) {
                $votosAlcalde = $alcalde->votacion->sum('votes');
                $alcalde->votos_count = $votosAlcalde;
                $alcalde->photoURL = $alcalde->photo
                    ? Storage::disk('public')->url($alcalde->photo)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($alcalde->name);

                // Calculamos el porcentaje real
                $porcentajeReal = $this->totalVotos > 0
                    ? ($votosAlcalde / $this->totalVotos * 100)
                    : 0;

                // Guardamos el porcentaje real para mostrar
                $alcalde->porcentaje = Number::percentage($porcentajeReal, 1);

                // Calculamos un porcentaje exagerado para la barra
                // Usamos una función exponencial para amplificar las diferencias
                $porcentajeExagerado = $porcentajeReal > 0
                    ? pow($porcentajeReal, 1.25)
                    : 0;

                // Normalizamos para que el máximo no exceda el 100%
                $alcalde->porcentaje_barra = $porcentajeExagerado . '%';

                return $alcalde;
            })
            ->sortByDesc('votos_count')
            ->values();
    }
}; ?>

<div>
    <main
        {{ !$escrutinioCompleto ? 'wire:poll.1s.keep-alive=refresh' : '' }} class="px-[96px] py-[54px] pb-[196px] h-screen grid grid-cols-3 gap-2">
        <div class="bg-blue-900 text-gray-100 p-8 rounded-lg col-span-2">
            <!-- Header con título y conteo de mesas -->
            <div class="grid grid-cols-3 mb-12">
                <h1 class="text-6xl font-bold col-span-2">Alcalde 2024</h1>
                <aside class="text-right">
                    <p class="text-4xl font-bold">{{ $porcentajeMesas }}</p>
                    <p class="uppercase text-blue-200">Mesas escrutadas</p>
                </aside>
            </div>

            <!-- Resultados -->
            <div class="space-y-2">
                @foreach($alcaldes as $index => $alcalde)
                    @if($alcalde->number < 998)
                        {{-- Excluir blancos y nulos --}}
                        <div
                            class="grid grid-cols-12 gap-x-6 items-center p-4 {{ $index === 0 ? 'bg-blue-800 rounded-lg' : '' }}">
                            <!-- Foto y nombre -->
                            <div class="col-span-7 flex items-center gap-6">
                                <img
                                    src="{{ $alcalde->photoURL }}"
                                    class="rounded-full object-cover border-4 bg-[{{ $alcalde->color }}] border-[{{ $alcalde->color }}] {{ $index === 0 ? 'w-32 h-32' : 'w-24 h-24' }}"
                                    alt="{{ $alcalde->name }}"
                                >
                                <div>
                                    <h3 class="{{ $index === 0 ? 'text-4xl' : 'text-3xl' }}">
                                        {{ $alcalde->name }}
                                    </h3>
                                    <p class="text-xl uppercase text-blue-200 mt-2">
                                        {{ $alcalde->is_independent ? 'Independiente' : $alcalde->partido?->abbr }}
                                        @if($alcalde->pacto)
                                            • {{ $alcalde->pacto->name }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Votos -->
                            <div class="col-span-2 text-right {{ $index === 0 ? 'text-5xl' : 'text-4xl' }} font-bold">
                                {{ number_format($alcalde->votos_count) }}
                                <p class="text-lg text-blue-200">votos válidos</p>
                            </div>

                            <!-- Barra de progreso y porcentaje -->
                            <div class="col-span-3">
                                <div
                                    class="relative {{ $index === 0 ? 'h-8' : 'h-6' }} bg-blue-950 rounded-md overflow-hidden">
                                    <div
                                        class="absolute h-full transition-all duration-200 ease-out rounded-md"
                                        style="width: {{ $alcalde->porcentaje_barra }}; background-color: {{ $alcalde->color }}"
                                    ></div>
                                </div>
                                <p class="text-right text-3xl font-bold mt-2">
                                    {{ $alcalde->porcentaje }}
                                </p>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Footer con última actualización -->
            @if(!$escrutinioCompleto)
                <div class="mt-12 text-center text-blue-200 text-xl">
                    <p>Resultados preliminares. Última actualización: {{ now('America/Santiago')->format('H:i:s') }}</p>
                </div>
            @else
                <div class="mt-12 text-center text-blue-200 text-xl">
                    <p>Resultados preliminares. Vea los resultados oficiales en <strong>servel.cl</strong>
                    </p>
                </div>
            @endif
        </div>
    </main>
</div>
