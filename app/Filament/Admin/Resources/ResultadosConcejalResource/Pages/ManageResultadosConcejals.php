<?php

namespace App\Filament\Admin\Resources\ResultadosConcejalResource\Pages;

use App\Filament\Admin\Resources\ResultadosConcejalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageResultadosConcejals extends ManageRecords
{
    protected static string $resource = ResultadosConcejalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
