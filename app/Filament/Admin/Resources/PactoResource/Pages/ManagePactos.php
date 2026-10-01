<?php

namespace App\Filament\Admin\Resources\PactoResource\Pages;

use App\Filament\Admin\Resources\PactoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ManagePactos extends ListRecords
{
    protected static string $resource = PactoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
