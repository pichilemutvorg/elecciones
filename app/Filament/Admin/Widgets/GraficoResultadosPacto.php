<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Concejal;
use App\Models\Mesa;
use App\Models\ResultadosConcejal;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Collection;

class GraficoResultadosPacto extends ChartWidget
{
    protected static ?string $heading = 'Votación por Pacto Electoral - Concejales';

    protected static ?string $pollingInterval = '1s';

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => false,
            ],
        ],
        'elements' => [
            'bar' => [
                'borderWidth' => 0,
            ],
        ],
    ];

    protected function getData(): array
    {
        // Obtener todos los concejales con sus relaciones
        $concejales = Concejal::with(['pacto', 'subpacto', 'partido', 'votacion'])->get();

        // Agrupar votos por pacto usando colecciones y sus letras
        $votosPorPacto = $concejales->groupBy(function ($concejal) {
            if (in_array($concejal->name, ['Blancos', 'Nulos'])) {
                return $concejal->name;
            }

            return $concejal->pacto?->letter ?? 'IND';
        })->map(function (Collection $grupo) {
            return $grupo->sum(function ($concejal) {
                return $concejal->votacion->sum('votes');
            });
        })->sortDesc();

        // Generar colores para cada pacto usando la letra del pacto como identificador consistente
        $colors = $votosPorPacto->keys()->map(function ($pactoName) use ($concejales) {
            // Buscar el primer concejal del pacto para obtener la letra
            $pactoLetter = $concejales->first(function ($concejal) use ($pactoName) {
                return $concejal->pacto?->name === $pactoName;
            })?->pacto?->letter;

            return match (true) {
                $pactoName === 'Votos Blancos' => '#e5e7eb',
                $pactoName === 'Votos Nulos' => '#71717a',
                $pactoName === 'Independientes Sin Pacto' => '#94a3b8',
                // Usar la letra del pacto para generar un color consistente
                $pactoLetter !== null => '#'.substr(md5($pactoLetter), 0, 6),
                default => '#'.substr(md5($pactoName), 0, 6),
            };
        });

        return [
            'labels' => $votosPorPacto->keys()->toArray(),
            'datasets' => [
                [
                    'label' => 'Votos',
                    'data' => $votosPorPacto->values()->toArray(),
                    'backgroundColor' => $colors->toArray(),
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getDescription(): ?string
    {
        $countedMesa = ResultadosConcejal::distinct('mesa_id')->count();
        $allMesas = Mesa::count();
        $percentage = round($countedMesa / $allMesas * 100, 1);

        return "Resultados con un {$percentage}% de las mesas escrutadas.";
    }
}
