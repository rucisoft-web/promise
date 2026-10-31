<?php

namespace App\Filament\Resources\ResourcePool\Pages;

use App\Filament\Resources\ResourcePool\ResourcePoolResource;
use Filament\Resources\Pages\ManageRecords;

class ManageResourcePool extends ManageRecords
{
    protected static string $resource = ResourcePoolResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
