<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MesaResource\Pages\ManageMesas;
use App\Models\Mesa;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MesaResource extends Resource
{
    protected static ?string $model = Mesa::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Votación';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('number')
                    ->label('Número de mesa')
                    ->required(),
                Select::make('local_id')
                    ->label('Local de votación')
                    ->relationship('local', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('Número de mesa')
                    ->searchable(),
                TextColumn::make('local.name'),
            ])
            ->filters([
                SelectFilter::make('local_id')
                    ->label('Local de votación')
                    ->relationship('local', 'name')
                    ->placeholder('Buscar por local de votación'),
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
            'index' => ManageMesas::route('/'),
        ];
    }
}
