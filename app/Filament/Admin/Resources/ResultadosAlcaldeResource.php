<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ResultadosAlcaldeResource\Pages;
use App\Models\Mesa;
use App\Models\ResultadosAlcalde;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

class ResultadosAlcaldeResource extends Resource
{
    protected static ?string $model = ResultadosAlcalde::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('mesa_id')
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
                Forms\Components\Select::make('alcalde_id')
                    ->label('Candidato')
                    ->relationship(
                        'alcalde',
                        'name',
                        fn ($query) => $query->orderBy('number')
                    )
                    ->nullable()
                    ->required(),
                Forms\Components\TextInput::make('votes')
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
                Tables\Columns\TextColumn::make('mesa.local.name')
                    ->label('Local'),
                Tables\Columns\TextColumn::make('mesa.number')
                    ->label('Mesa'),
                Tables\Columns\TextColumn::make('alcalde.name')
                    ->label('Candidato'),
                Tables\Columns\TextColumn::make('votes')
                    ->label('Votación'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('mesa.local_id')
                    ->label('Local')
                    ->relationship('mesa.local', 'name'),
                Tables\Filters\SelectFilter::make('mesa_id')
                    ->label('Mesa')
                    ->relationship('mesa', 'number'),
                Tables\Filters\SelectFilter::make('alcalde_id')
                    ->label('Candidato')
                    ->relationship('alcalde', 'name'),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageResultadosAlcaldes::route('/'),
        ];
    }
}
