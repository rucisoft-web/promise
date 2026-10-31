<?php

namespace App\Filament\Resources\Stakeholders\Pages;

use App\Filament\Resources\Stakeholders\StakeholderResource;
use Filament\Resources\Pages\ManageRecords;

class ManageStakeholders extends ManageRecords
{
    protected static string $resource = StakeholderResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
