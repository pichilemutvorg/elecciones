<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ResultadosAlcaldeResource\Pages\ManageResultadosAlcaldes;
use App\Models\Mesa;
use App\Models\ResultadosAlcalde;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ResultadosAlcaldeResource extends Resource
{
    protected static ?string $model = ResultadosAlcalde::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('mesa_id')
                    ->label('Mesa')
                    ->relationship(
                        'mesa',
                        'number',
                        fn ($query) => $query
                            ->with('local_id')
                            ->orderBy('local_id')
                            ->orderBy('number')
                    )
                    ->searchable()
                    ->preload()
                    ->options(function () {
                        return Mesa::with('local')
                            ->orderBy('local_id')
                            ->orderBy('number')
                            ->get()
                            ->mapWithKeys(function ($mesa) {
                                return [$mesa->id => "{$mesa->local->name} – {$mesa->number}"];
                            });
                    })
                    ->optionsLimit(10)
                    ->nullable()
                    ->required(),
                Select::make('alcalde_id')
                    ->label('Candidato')
                    ->relationship(
                        'alcalde',
                        'name',
                        fn ($query) => $query->orderBy('number')
                    )
                    ->nullable()
                    ->required(),
                TextInput::make('votes')
                    ->label('Votación')
                    ->suffix('votos')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('mesa.local.name')
                    ->label('Local'),
                TextColumn::make('mesa.number')
                    ->label('Mesa'),
                TextColumn::make('alcalde.name')
                    ->label('Candidato'),
                TextColumn::make('votes')
                    ->label('Votación'),
            ])
            ->filters([
                SelectFilter::make('mesa.local_id')
                    ->label('Local')
                    ->relationship('mesa.local', 'name'),
                SelectFilter::make('mesa_id')
                    ->label('Mesa')
                    ->relationship('mesa', 'number'),
                SelectFilter::make('alcalde_id')
                    ->label('Candidato')
                    ->relationship('alcalde', 'name'),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageResultadosAlcaldes::route('/'),
        ];
    }
}
