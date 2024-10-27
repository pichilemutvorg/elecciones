<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ResultadosConcejalResource\Pages;
use App\Models\ResultadosConcejal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

class ResultadosConcejalResource extends Resource
{
    protected static ?string $model = ResultadosConcejal::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Resultados Concejales';

    protected static ?string $label = 'Resultados Concejales';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('mesa_id')
                    ->relationship('mesa', 'id')
                    ->required(),
                Forms\Components\Select::make('concejal_id')
                    ->relationship('concejal', 'name')
                    ->required(),
                Forms\Components\TextInput::make('votes')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mesa.local.name')
                    ->width('200px')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mesa.id')
                    ->numeric()
                    ->width('4ch')
                    ->sortable(),
                Tables\Columns\TextColumn::make('concejal.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('votes')
                    ->label('Votación')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('local_id')
                    ->relationship('mesa.local', 'name')
                    ->label('Local'),
                Tables\Filters\SelectFilter::make('mesa_id')
                    ->relationship('mesa', 'id')
                    ->label('Mesa'),
                Tables\Filters\SelectFilter::make('concejal_id')
                    ->relationship('concejal', 'name')
                    ->label('Concejal'),
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
            'index' => Pages\ManageResultadosConcejals::route('/'),
        ];
    }
}
