<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PactoResource\Pages\ManagePactos;
use App\Models\Pacto;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PactoResource extends Resource
{
    protected static ?string $model = Pacto::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Coaliciones';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Pacto')
                    ->required(),
                TextInput::make('letter')
                    ->label('Letra'),
                TextInput::make('icon')
                    ->label('Ícono'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('letter')
                    ->label('Letra')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Pacto')
                    ->searchable(),
                TextColumn::make('icon')
                    ->label('Ícono'),
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
            'index' => ManagePactos::route('/'),
        ];
    }
}
