<?php

namespace App\Filament\Resources\ChangeRequests\Pages;

use App\Filament\Resources\ChangeRequests\ChangeRequestResource;
use Filament\Resources\Pages\ManageRecords;

class ManageChangeRequests extends ManageRecords
{
    protected static string $resource = ChangeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
