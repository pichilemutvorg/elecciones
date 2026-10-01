<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AlcaldeResource\Pages\ManageAlcaldes;
use App\Models\Alcalde;
use App\Models\ResultadosAlcalde;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Number;
use Storage;

class AlcaldeResource extends Resource
{
    protected static ?string $model = Alcalde::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Candidatos';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('number')
                    ->label('Número')
                    ->numeric()
                    ->required(),
                Toggle::make('is_independent')
                    ->label('Independiente'),
                TextInput::make('name')
                    ->label('Nombre completo')
                    ->required(),
                ColorPicker::make('color')
                    ->label('Color identificador')
                    ->nullable(),
                FileUpload::make('photo')
                    ->label('Fotografía')
                    ->image()
                    ->imageEditor()
                    ->nullable()
                    ->directory('alcaldes'),
                Select::make('partido_id')
                    ->label('Partido')
                    ->relationship('partido', 'name')
                    ->nullable(),
                Select::make('pacto_id')
                    ->label('Pacto')
                    ->relationship('pacto', 'name')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('Número')
                    ->alignRight()
                    ->width('6ch')
                    ->state(fn (Alcalde $record): string => in_array($record->name, ['Blancos', 'Nulos'])
                        ? ''
                        : "{$record->pacto?->letter} {$record->number}")
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Candidato')
                    ->searchable()
                    ->formatStateUsing(function (Alcalde $record) {
                        $photoUrl = $record->photo
                            ? Storage::disk('public')->url($record->photo)
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
                ColorColumn::make('color')
                    ->label('Color')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn (Alcalde $record) => 'https://ui-avatars.com/api/?name='.urlencode($record->name))
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('votacion_sum_votes')
                    ->label('Votos')
                    ->numeric()
                    ->alignRight()
                    ->sortable()
                    ->sum('votacion', 'votes')
                    ->summarize(
                        Sum::make()
                            ->label('Total')
                    ),
                TextColumn::make('percentage')
                    ->label('%')
                    ->alignRight()
                    ->state(function (Alcalde $record): string {
                        $totalVotes = ResultadosAlcalde::sum('votes');

                        return $totalVotes > 0
                            ? Number::percentage($record->votacion_sum_votes / $totalVotes * 100, 1)
                            : '0.0';
                    }),
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
            'index' => ManageAlcaldes::route('/'),
        ];
    }
}
