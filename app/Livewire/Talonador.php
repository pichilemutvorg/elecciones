<?php

namespace App\Livewire;

use App\Models\Alcalde;
use App\Models\Concejal;
use App\Models\Local;
use App\Models\Mesa;
use App\Models\ResultadosAlcalde;
use App\Models\ResultadosConcejal;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Talonador extends Component implements HasForms
{
    use InteractsWithForms;

    public array $data = [
        'cargo' => null,
        'local' => null,
        'mesa' => null,
        'votos' => [],
    ];

    public function mount(): void
    {
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->form->fill([
            'cargo' => null,
            'local' => null,
            'mesa' => null,
            'votos' => [],
        ]);
    }

    protected function getCandidatos(): Collection
    {
        if (empty($this->data['cargo'])) {
            return collect();
        }

        if ($this->data['cargo'] === 'alcalde') {
            return Alcalde::query()
                ->orderBy('number')
                ->get();
        }

        return Concejal::query()
            ->orderBy('number')
            ->get();
    }

    protected function loadExistingVotes(int $mesaId, string $cargo): array
    {
        $votos = [];

        if ($cargo === 'alcalde') {
            $resultados = ResultadosAlcalde::where('mesa_id', $mesaId)->get();
            foreach ($resultados as $resultado) {
                $votos[$resultado->alcalde_id] = $resultado->votes;
            }
        } else {
            $resultados = ResultadosConcejal::where('mesa_id', $mesaId)->get();
            foreach ($resultados as $resultado) {
                $votos[$resultado->concejal_id] = $resultado->votes;
            }
        }

        return $votos;
    }

    protected function getMesaStatus(Mesa $mesa, string $cargo): array
    {
        $isAvailable = true;
        $suffix = '';

        $model = $cargo === 'alcalde' ? ResultadosAlcalde::class : ResultadosConcejal::class;
        $totalCandidatos = $cargo === 'alcalde' ? Alcalde::count() : Concejal::count();

        $votosRegistrados = $model::where('mesa_id', $mesa->id)->count();

        if ($votosRegistrados === $totalCandidatos) {
            $isAvailable = false;
        } elseif ($votosRegistrados > 0) {
            $suffix = ' (incompleto)';
            $isAvailable = true;
        }

        return [
            'isAvailable' => $isAvailable,
            'suffix' => $suffix,
            'hasVotes' => $votosRegistrados > 0,
        ];
    }

    public function calculateTotal(): int
    {
        return collect($this->data['votos'] ?? [])
            ->filter()
            ->sum(fn ($voto) => (int) $voto);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Seleccionar mesa de votación')
                    ->description('Seleccione el tipo de elección y la mesa para ingresar resultados')
                    ->columns(3)
                    ->schema([
                        Select::make('cargo')
                            ->label('Cargo a elegir')
                            ->options([
                                'alcalde' => 'Alcalde',
                                'concejal' => 'Concejal',
                            ])
                            ->afterStateUpdated(function (Set $set) {
                                $set('local', null);
                                $set('mesa', null);
                                $set('votos', []);
                            })
                            ->live()
                            ->required(),

                        Select::make('local')
                            ->label('Local de votación')
                            ->options(Local::pluck('name', 'id'))
                            ->afterStateUpdated(function (Set $set) {
                                $set('mesa', null);
                                $set('votos', []);
                            })
                            ->disabled(fn (Get $get) => empty($get('cargo')))
                            ->live()
                            ->required(),

                        Select::make('mesa')
                            ->label('Mesa de votación')
                            ->placeholder(fn (Get $get): string => empty($get('local')) ?
                                'Primero seleccione un local' :
                                'Seleccione una mesa'
                            )
                            ->options(function (Get $get) {
                                if (! $get('local') || ! $get('cargo')) {
                                    return [];
                                }

                                $mesas = Mesa::where('local_id', $get('local'))
                                    ->orderBy('id')
                                    ->get();

                                $options = [];
                                foreach ($mesas as $mesa) {
                                    $status = $this->getMesaStatus($mesa, $get('cargo'));

                                    if ($status['isAvailable']) {
                                        $options[$mesa->id] = 'Mesa '.$mesa->number.$status['suffix'];
                                    }
                                }

                                return $options;
                            })
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                if ($state) {
                                    $votos = $this->loadExistingVotes($state, $get('cargo'));
                                    $set('votos', $votos);
                                } else {
                                    $set('votos', []);
                                }
                            })
                            ->live()
                            ->required()
                            ->disabled(fn (Get $get) => empty($get('local'))),
                    ]),

                Section::make('Ingreso de votos')
                    ->description(function (Get $get) {
                        $mesaId = $get('mesa');
                        if ($mesaId) {
                            $status = $this->getMesaStatus(Mesa::find($mesaId), $get('cargo'));
                            if ($status['hasVotes']) {
                                return 'Esta mesa tiene resultados parciales. Puede modificar los valores existentes.';
                            }
                        }

                        return 'Ingrese la cantidad de votos para cada candidato';
                    })
                    ->visible(fn (Get $get) => filled($get('cargo')) &&
                        filled($get('local')) &&
                        filled($get('mesa'))
                    )
                    ->schema(function (Get $get) {
                        if (empty($get('mesa'))) {
                            return [];
                        }

                        $schema = [];
                        $candidatos = $this->getCandidatos();

                        if ($get('cargo') === 'alcalde') {
                            $fields = [];
                            foreach ($candidatos as $candidato) {
                                $label = in_array($candidato->name, ['Blancos', 'Nulos'])
                                    ? $candidato->name
                                    : "{$candidato->number} - {$candidato->name}";

                                $fields["voto_{$candidato->id}"] = TextInput::make("votos.{$candidato->id}")
                                    ->label($label)
                                    ->numeric()
                                    ->default(0)
                                    ->rules(['nullable', 'numeric', 'min:0', 'max:400'])
                                    ->live()
                                    ->inputMode('numeric')
                                    ->afterStateUpdated(function ($state) {
                                        return $state === '' || $state === null ? 0 : (int) $state;
                                    });
                            }

                            $schema[] = Grid::make()
                                ->schema($fields)
                                ->columns(3);
                        } else {
                            $candidatosPorPacto = $candidatos->groupBy('pacto.name');

                            foreach ($candidatosPorPacto as $pacto => $candidatosDelPacto) {
                                $fields = [];
                                foreach ($candidatosDelPacto as $candidato) {
                                    $label = in_array($candidato->name, ['Blancos', 'Nulos'])
                                        ? $candidato->name
                                        : "{$candidato->number} - {$candidato->name}";

                                    $fields["voto_{$candidato->id}"] = TextInput::make("votos.{$candidato->id}")
                                        ->label($label)
                                        ->numeric()
                                        ->default(0)
                                        ->rules(['nullable', 'numeric', 'min:0', 'max:400'])
                                        ->live()
                                        ->inputMode('numeric')
                                        ->afterStateUpdated(function ($state) {
                                            return $state === '' || $state === null ? 0 : (int) $state;
                                        });
                                }

                                $schema[] = Section::make($pacto ?: 'Sin Pacto')
                                    ->schema([
                                        Grid::make()
                                            ->schema($fields)
                                            ->columns(3),
                                    ])
                                    ->collapsible();
                            }
                        }

                        $schema[] = Placeholder::make('total_votos')
                            ->label('Total de votos ingresados')
                            ->content(fn () => $this->calculateTotal());

                        return $schema;
                    }),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        try {
            $formData = $this->form->validate();
            $data = $formData['data'];
            $votos = $data['votos'] ?? [];

            if (empty($votos)) {
                $this->addError('votos', 'Debe ingresar los votos para los candidatos.');

                return;
            }

            DB::beginTransaction();

            try {
                $model = $data['cargo'] === 'alcalde' ? ResultadosAlcalde::class : ResultadosConcejal::class;
                $modelId = $data['cargo'] === 'alcalde' ? 'alcalde_id' : 'concejal_id';

                $model::where('mesa_id', $data['mesa'])->delete();

                foreach ($votos as $candidatoId => $cantidadVotos) {
                    $model::create([
                        'mesa_id' => $data['mesa'],
                        $modelId => $candidatoId,
                        'votes' => $cantidadVotos,
                    ]);
                }

                DB::commit();
                session()->flash('success', 'Los resultados se han guardado correctamente.');

                $this->resetForm();

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            $this->addError('form', 'Hubo un error procesando el formulario: '.$e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.talonador');
    }
}
