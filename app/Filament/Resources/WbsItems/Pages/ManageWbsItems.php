<?php

namespace App\Filament\Resources\WbsItems\Pages;

use App\Filament\Resources\WbsItems\WbsItemResource;
use Filament\Resources\Pages\ManageRecords;

class ManageWbsItems extends ManageRecords
{
    protected static string $resource = WbsItemResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
