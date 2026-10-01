<?php

namespace App\Filament\Admin\Resources\PartidoResource\Pages;

use App\Filament\Admin\Resources\PartidoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ManagePartidos extends ListRecords
{
    protected static string $resource = PartidoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
