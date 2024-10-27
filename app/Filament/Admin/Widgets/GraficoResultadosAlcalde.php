<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Alcalde;
use App\Models\Mesa;
use App\Models\ResultadosAlcalde;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Str;

class GraficoResultadosAlcalde extends ChartWidget
{
    protected static ?string $heading = 'Votación de candidatos a alcalde';

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
        $alcaldes = Alcalde::all();

        $labels = $alcaldes->map(fn ($alcalde) => Str::of($alcalde->name)->explode(' ')->get(1) ?? $alcalde->name
        )->toArray();

        $votes = $alcaldes->map(fn ($alcalde) => $alcalde->votacion()->sum('votes')
        )->toArray();

        $colors = $alcaldes->map(fn ($alcalde) => $alcalde->color ?? ($alcalde->name === 'Blancos' ? '#e5e7eb' : '#71717a')
        )->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Votos',
                    'data' => $votes,
                    'backgroundColor' => $colors,
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
        $countedMesa = ResultadosAlcalde::distinct('mesa_id')->count();
        $allMesas = Mesa::count();
        $percentage = $allMesas > 0 ? round($countedMesa / $allMesas * 100, 1) : 0;

        return "Resultados con un {$percentage}% de las mesas escrutadas.";
    }
}
