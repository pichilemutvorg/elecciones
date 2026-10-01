<?php

namespace App\Filament\Admin\Resources\ResultadosAlcaldeResource\Pages;

use App\Filament\Admin\Resources\ResultadosAlcaldeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ManageResultadosAlcaldes extends ListRecords
{
    protected static string $resource = ResultadosAlcaldeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
