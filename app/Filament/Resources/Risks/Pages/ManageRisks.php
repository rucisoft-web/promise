<?php

namespace App\Filament\Resources\Risks\Pages;

use App\Filament\Resources\Risks\RiskResource;
use Filament\Resources\Pages\ManageRecords;

class ManageRisks extends ManageRecords
{
    protected static string $resource = RiskResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
