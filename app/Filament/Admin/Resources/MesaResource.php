<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MesaResource\Pages;
use App\Models\Mesa;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MesaResource extends Resource
{
    protected static ?string $model = Mesa::class;

    protected static ?string $navigationGroup = 'Votación';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                Tables\Columns\TextColumn::make('number')
                    ->label('Número de mesa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('local.name'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('local_id')
                    ->label('Local de votación')
                    ->relationship('local', 'name')
                    ->placeholder('Buscar por local de votación'),
            ])
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
            'index' => Pages\ManageMesas::route('/'),
        ];
    }
}
