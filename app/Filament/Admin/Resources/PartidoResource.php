<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PartidoResource\Pages;
use App\Models\Partido;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PartidoResource extends Resource
{
    protected static ?string $model = Partido::class;

    protected static ?string $navigationGroup = 'Coaliciones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->label('Nombre'),
                Forms\Components\TextInput::make('abbr')->required()->label('Abreviatura'),
                Forms\Components\TextInput::make('icon')->label('Icono'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('abbr')->searchable(),
                Tables\Columns\TextColumn::make('icon')->searchable(),
            ])
            ->filters([

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
            'index' => Pages\ManagePartidos::route('/'),
        ];
    }
}
