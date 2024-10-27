<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Concejal;
use App\Models\Pacto;
use App\Models\ResultadosConcejal;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Collection;

class TablaResultadosConcejales extends BaseWidget
{
    protected static ?string $heading = '';

    protected int $totalEscanos = 6;

    public function table(Table $table): Table
    {
        $electos = $this->obtenerElectos();

        return $table
            ->query(
                Concejal::query()
                    ->whereIn('id', $electos->pluck('id'))
                    ->withSum('votacion', 'votes')
                    ->orderByRaw(
                        'CASE id '.
                        $electos->pluck('id')->map(function ($id, $index) {
                            return "WHEN $id THEN $index";
                        })->join(' ').
                        ' END'
                    )
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Concejales electos')
                    ->formatStateUsing(function (Concejal $record) {
                        $photoUrl = $record->photo
                            ? \Storage::disk('public')->url($record->photo)
                            : 'https://ui-avatars.com/api/?name='.urlencode($record->name);

                        // Generar color consistente basado en la letra del pacto o nombre
                        $color = match (true) {
                            $record->pacto?->letter !== null => '#'.substr(md5($record->pacto->letter), 0, 6),
                            default => '#'.substr(md5($record->name), 0, 6),
                        };

                        $style = "border: 2px solid {$color};";

                        $partidoText = $record->is_independent
                            ? 'IND'
                            : ($record->partido?->abbr ?? '');

                        $pactoText = $record->pacto?->name ?? '';

                        $subtitle = implode(' • ', array_filter([$pactoText, $partidoText]));

                        return "
                            <div class='flex items-center gap-1'>
                                <img
                                    src='{$photoUrl}'
                                    class='w-8 h-8 rounded-full object-cover'
                                    style='{$style}'
                                    alt='{$record->name}'
                                />
                                <div class='flex flex-col'>
                                    <span class='font-medium'>{$record->name}</span>
                                    <span class='text-xs text-gray-500'>{$subtitle}</span>
                                </div>
                            </div>
                        ";
                    })
                    ->html(),

                TextColumn::make('votacion_sum_votes')
                    ->label('Votos')
                    ->alignRight(),

                TextColumn::make('percentage')
                    ->label('%')
                    ->alignRight()
                    ->state(function (Concejal $record): string {
                        $totalVotes = ResultadosConcejal::sum('votes');

                        return $totalVotes > 0
                            ? \Number::percentage($record->votacion_sum_votes / $totalVotes * 100, 1)
                            : '0.0';
                    }),
            ])
            ->paginated(false)
            ->poll('1s');
    }

    protected function obtenerElectos(): Collection
    {
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

        // 2. Calcular coeficientes D'Hondt para cada pacto
        $divisores = collect();
        foreach ($votosPorPacto as $pactoId => $datos) {
            for ($i = 1; $i <= $this->totalEscanos; $i++) {
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
            ->take($this->totalEscanos)
            ->groupBy('pacto_id')
            ->map(function ($grupo) {
                return $grupo->count();
            });

        // 4. Seleccionar los candidatos más votados de cada pacto según escaños asignados
        $electos = collect();
        foreach ($asignaciones as $pactoId => $numEscanos) {
            $candidatosPacto = $votosPorPacto[$pactoId]['concejales']
                ->take($numEscanos);
            $electos = $electos->concat($candidatosPacto);
        }

        return $electos->sortByDesc('votacion_sum_votes');
    }
}
