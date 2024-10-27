<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Alcalde;
use App\Models\ResultadosAlcalde;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TablaResultadosAlcalde extends BaseWidget
{
    //    protected static ?string $heading = 'Votación y porcentaje de candidatos a alcalde';
    protected static ?string $heading = '';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Alcalde::query()
                    ->withSum('votacion', 'votes')
                    ->orderByDesc('votacion_sum_votes')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Candidato')
                    ->formatStateUsing(function (Alcalde $record) {
                        $photoUrl = $record->photo
                            ? \Storage::disk('public')->url($record->photo)
                            : 'https://ui-avatars.com/api/?name='.urlencode($record->name);

                        $style = $record->color
                            ? "border: 2px solid {$record->color};"
                            : 'border: 2px solid #e5e7eb;';

                        return "
                            <div class='flex items-center gap-2'>
                                <img
                                    src='{$photoUrl}'
                                    class='w-8 h-8 rounded-full object-cover'
                                    style='{$style}'
                                    alt='{$record->name}'
                                />
                                <span>{$record->name}</span>
                            </div>
                        ";
                    })
                    ->html(),
                TextColumn::make('votacion_sum_votes')
                    ->label('Votos')
                    ->width('6ch')
                    ->numeric()
                    ->alignRight(),
                TextColumn::make('percentage')
                    ->label('%')
                    ->width('5ch')
                    ->state(function ($record) {
                        $totalVotes = $this->getTotalVotes();

                        return $totalVotes > 0
                            ? \Number::percentage($record->votacion_sum_votes / $totalVotes * 100, 1)
                            : '0.0';
                    }),
            ])
            ->paginated(false)
            ->poll('1s');
    }

    private function getTotalVotes(): int
    {
        return ResultadosAlcalde::sum('votes');
    }
}
