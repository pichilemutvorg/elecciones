<?php

namespace App\Filament\Admin\Resources\AlcaldeResource\Pages;

use App\Filament\Admin\Resources\AlcaldeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ManageAlcaldes extends ListRecords
{
    protected static string $resource = AlcaldeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
