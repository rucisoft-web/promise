<?php

namespace App\Filament\Resources\QualityChecks\Pages;

use App\Filament\Resources\QualityChecks\QualityCheckResource;
use Filament\Resources\Pages\ManageRecords;

class ManageQualityChecks extends ManageRecords
{
    protected static string $resource = QualityCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
