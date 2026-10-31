<?php

namespace App\Filament\Resources\CommunicationPlans\Pages;

use App\Filament\Resources\CommunicationPlans\CommunicationPlanResource;
use Filament\Resources\Pages\ManageRecords;

class ManageCommunicationPlans extends ManageRecords
{
    protected static string $resource = CommunicationPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
