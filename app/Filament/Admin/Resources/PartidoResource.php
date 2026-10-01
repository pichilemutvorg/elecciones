<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PartidoResource\Pages\ManagePartidos;
use App\Models\Partido;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PartidoResource extends Resource
{
    protected static ?string $model = Partido::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Coaliciones';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->label('Nombre'),
                TextInput::make('abbr')->required()->label('Abreviatura'),
                TextInput::make('icon')->label('Icono'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('abbr')->searchable(),
                TextColumn::make('icon')->searchable(),
            ])
            ->filters([

            ])
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
            'index' => ManagePartidos::route('/'),
        ];
    }
}
