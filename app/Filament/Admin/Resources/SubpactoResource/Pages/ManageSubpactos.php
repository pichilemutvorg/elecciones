<?php

namespace App\Filament\Admin\Resources\SubpactoResource\Pages;

use App\Filament\Admin\Resources\SubpactoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ManageSubpactos extends ListRecords
{
    protected static string $resource = SubpactoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
