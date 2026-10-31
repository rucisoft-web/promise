<?php

namespace App\Filament\Resources\Contracts\Pages;

use App\Filament\Resources\Contracts\ContractResource;
use Filament\Resources\Pages\ManageRecords;

class ManageContracts extends ManageRecords
{
    protected static string $resource = ContractResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
