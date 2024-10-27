<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AlcaldeResource\Pages;
use App\Models\Alcalde;
use App\Models\ResultadosAlcalde;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;

class AlcaldeResource extends Resource
{
    protected static ?string $model = Alcalde::class;

    protected static ?string $navigationGroup = 'Candidatos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('number')
                    ->label('Número')
                    ->numeric()
                    ->required(),
                Forms\Components\Toggle::make('is_independent')
                    ->label('Independiente'),
                Forms\Components\TextInput::make('name')
                    ->label('Nombre completo')
                    ->required(),
                Forms\Components\ColorPicker::make('color')
                    ->label('Color identificador')
                    ->nullable(),
                Forms\Components\FileUpload::make('photo')
                    ->label('Fotografía')
                    ->image()
                    ->imageEditor()
                    ->nullable()
                    ->directory('alcaldes'),
                Forms\Components\Select::make('partido_id')
                    ->label('Partido')
                    ->relationship('partido', 'name')
                    ->nullable(),
                Forms\Components\Select::make('pacto_id')
                    ->label('Pacto')
                    ->relationship('pacto', 'name')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Número')
                    ->alignRight()
                    ->width('6ch')
                    ->state(fn (Alcalde $record): string => in_array($record->name, ['Blancos', 'Nulos'])
                        ? ''
                        : "{$record->pacto?->letter} {$record->number}")
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Candidato')
                    ->searchable()
                    ->formatStateUsing(function (Alcalde $record) {
                        $photoUrl = $record->photo
                            ? \Storage::disk('public')->url($record->photo)
                            : 'https://ui-avatars.com/api/?name='.urlencode($record->name);

                        $style = $record->color
                            ? "border: 2px solid {$record->color};"
                            : 'border: 2px solid #e5e7eb;';

                        $partidoText = $record->is_independent
                            ? 'IND'
                            : ($record->partido?->abbr ?? '');

                        $pactoText = $record->pacto?->name ?? '';

                        $subtitle = implode(' • ', array_filter([$pactoText, $partidoText]));

                        return "
                            <div class='flex items-center gap-2'>
                                <img
                                    src='{$photoUrl}'
                                    class='w-8 h-8 rounded-full object-cover'
                                    style='{$style}'
                                    alt='{$record->name}'
                                />
                                <div class='flex flex-col'>
                                    <span class='font-medium'>{$record->name}</span>
                                    <span class='text-sm text-gray-500'>{$subtitle}</span>
                                </div>
                            </div>
                        ";
                    })
                    ->html(),
                Tables\Columns\ColorColumn::make('color')
                    ->label('Color')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn (Alcalde $record) => 'https://ui-avatars.com/api/?name='.urlencode($record->name))
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('votacion_sum_votes')
                    ->label('Votos')
                    ->numeric()
                    ->alignRight()
                    ->sortable()
                    ->sum('votacion', 'votes')
                    ->summarize(
                        Sum::make()
                            ->label('Total')
                    ),
                Tables\Columns\TextColumn::make('percentage')
                    ->label('%')
                    ->alignRight()
                    ->state(function (Alcalde $record): string {
                        $totalVotes = ResultadosAlcalde::sum('votes');

                        return $totalVotes > 0
                            ? \Number::percentage($record->votacion_sum_votes / $totalVotes * 100, 1)
                            : '0.0';
                    }),
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
            'index' => Pages\ManageAlcaldes::route('/'),
        ];
    }
}
