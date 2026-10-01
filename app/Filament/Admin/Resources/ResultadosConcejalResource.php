<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ResultadosConcejalResource\Pages\ManageResultadosConcejals;
use App\Models\ResultadosConcejal;
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

class ResultadosConcejalResource extends Resource
{
    protected static ?string $model = ResultadosConcejal::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Resultados Concejales';

    protected static ?string $label = 'Resultados Concejales';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('mesa_id')
                    ->relationship('mesa', 'id')
                    ->required(),
                Select::make('concejal_id')
                    ->relationship('concejal', 'name')
                    ->required(),
                TextInput::make('votes')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('mesa.local.name')
                    ->width('200px')
                    ->sortable(),
                TextColumn::make('mesa.id')
                    ->numeric()
                    ->width('4ch')
                    ->sortable(),
                TextColumn::make('concejal.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('votes')
                    ->label('Votación')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('local_id')
                    ->relationship('mesa.local', 'name')
                    ->label('Local'),
                SelectFilter::make('mesa_id')
                    ->relationship('mesa', 'id')
                    ->label('Mesa'),
                SelectFilter::make('concejal_id')
                    ->relationship('concejal', 'name')
                    ->label('Concejal'),
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
            'index' => ManageResultadosConcejals::route('/'),
        ];
    }
}
