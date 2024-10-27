<?php

namespace App\Filament\Admin\Resources\PartidoResource\Pages;

use App\Filament\Admin\Resources\PartidoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ManagePartidos extends ListRecords
{
    protected static string $resource = PartidoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
