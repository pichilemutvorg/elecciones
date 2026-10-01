<?php

namespace App\Filament\Admin\Resources\MesaResource\Pages;

use App\Filament\Admin\Resources\MesaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ManageMesas extends ListRecords
{
    protected static string $resource = MesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
