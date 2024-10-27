<?php

namespace App\Filament\Admin\Resources\ConcejalResource\Pages;

use App\Filament\Admin\Resources\ConcejalResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageConcejals extends ManageRecords
{
    protected static string $resource = ConcejalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
