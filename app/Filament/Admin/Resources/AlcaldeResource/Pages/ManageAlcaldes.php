<?php

namespace App\Filament\Admin\Resources\AlcaldeResource\Pages;

use App\Filament\Admin\Resources\AlcaldeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ManageAlcaldes extends ListRecords
{
    protected static string $resource = AlcaldeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
