<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ConcejalResource\Pages\ManageConcejals;
use App\Models\Concejal;
use App\Models\ResultadosConcejal;
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
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Number;
use Storage;

class ConcejalResource extends Resource
{
    protected static ?string $model = Concejal::class;

    protected static ?string $pluralModelLabel = 'concejales';

    protected static string|\UnitEnum|null $navigationGroup = 'Candidatos';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('number')
                    ->label('Número')
                    ->required()
                    ->numeric(),
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
                    ->directory('concejales'),
                Select::make('partido_id')
                    ->label('Partido')
                    ->relationship('partido', 'name')
                    ->nullable(),
                Select::make('pacto_id')
                    ->label('Pacto')
                    ->relationship('pacto', 'name')
                    ->nullable(),
                Select::make('subpacto_id')
                    ->label('Subpacto')
                    ->relationship('subpacto', 'name')
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
                    ->state(fn (Concejal $record): string => in_array($record->name, ['Blancos', 'Nulos'])
                        ? ''
                        : "{$record->pacto?->letter} {$record->number}")
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Candidato')
                    ->searchable()
                    ->formatStateUsing(function (Concejal $record) {
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
                TextColumn::make('partido.abbr')
                    ->label('Partido')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('pacto.name')
                    ->label('Pacto')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('subpacto.name')
                    ->label('Subpacto')
                    ->toggleable(),
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
                    ->state(function (Concejal $record): string {
                        $totalVotes = ResultadosConcejal::sum('votes');

                        return $totalVotes > 0
                            ? Number::percentage($record->votacion_sum_votes / $totalVotes * 100, 1)
                            : '0.0';
                    }),
            ])
            ->filters([
                SelectFilter::make('pacto_id')
                    ->label('Pacto')
                    ->relationship('pacto', 'name'),
                SelectFilter::make('subpacto_id')
                    ->label('Subpacto')
                    ->relationship('subpacto', 'name'),
                SelectFilter::make('partido_id')
                    ->label('Partido')
                    ->relationship('partido', 'name'),
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
            'index' => ManageConcejals::route('/'),
        ];
    }
}
