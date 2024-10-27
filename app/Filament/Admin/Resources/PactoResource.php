<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PactoResource\Pages;
use App\Models\Pacto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PactoResource extends Resource
{
    protected static ?string $model = Pacto::class;

    protected static ?string $navigationGroup = 'Coaliciones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Pacto')
                    ->required(),
                Forms\Components\TextInput::make('letter')
                    ->label('Letra'),
                Forms\Components\TextInput::make('icon')
                    ->label('Ícono'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('letter')
                    ->label('Letra')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Pacto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('icon')
                    ->label('Ícono'),
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
            'index' => Pages\ManagePactos::route('/'),
        ];
    }
}
