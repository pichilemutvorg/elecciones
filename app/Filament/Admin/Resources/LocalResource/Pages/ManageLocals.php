<?php

namespace App\Filament\Admin\Resources\LocalResource\Pages;

use App\Filament\Admin\Resources\LocalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ManageLocals extends ListRecords
{
    protected static string $resource = LocalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
