<?php

namespace App\Filament\Admin\Resources\ResultadosConcejalResource\Pages;

use App\Filament\Admin\Resources\ResultadosConcejalResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageResultadosConcejals extends ManageRecords
{
    protected static string $resource = ResultadosConcejalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
