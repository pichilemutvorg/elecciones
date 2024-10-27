<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ConcejalResource\Pages;
use App\Models\Concejal;
use App\Models\ResultadosConcejal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;

class ConcejalResource extends Resource
{
    protected static ?string $model = Concejal::class;

    protected static ?string $pluralModelLabel = 'concejales';

    protected static ?string $navigationGroup = 'Candidatos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('number')
                    ->label('Número')
                    ->required()
                    ->numeric(),
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
                    ->directory('concejales'),
                Forms\Components\Select::make('partido_id')
                    ->label('Partido')
                    ->relationship('partido', 'name')
                    ->nullable(),
                Forms\Components\Select::make('pacto_id')
                    ->label('Pacto')
                    ->relationship('pacto', 'name')
                    ->nullable(),
                Forms\Components\Select::make('subpacto_id')
                    ->label('Subpacto')
                    ->relationship('subpacto', 'name')
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
                    ->state(fn (Concejal $record): string => in_array($record->name, ['Blancos', 'Nulos'])
                        ? ''
                        : "{$record->pacto?->letter} {$record->number}")
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Candidato')
                    ->searchable()
                    ->formatStateUsing(function (Concejal $record) {
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
                Tables\Columns\TextColumn::make('partido.abbr')
                    ->label('Partido')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('pacto.name')
                    ->label('Pacto')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('subpacto.name')
                    ->label('Subpacto')
                    ->toggleable(),
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
                    ->state(function (Concejal $record): string {
                        $totalVotes = ResultadosConcejal::sum('votes');

                        return $totalVotes > 0
                            ? \Number::percentage($record->votacion_sum_votes / $totalVotes * 100, 1)
                            : '0.0';
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('pacto_id')
                    ->label('Pacto')
                    ->relationship('pacto', 'name'),
                Tables\Filters\SelectFilter::make('subpacto_id')
                    ->label('Subpacto')
                    ->relationship('subpacto', 'name'),
                Tables\Filters\SelectFilter::make('partido_id')
                    ->label('Partido')
                    ->relationship('partido', 'name'),
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
            'index' => Pages\ManageConcejals::route('/'),
        ];
    }
}
