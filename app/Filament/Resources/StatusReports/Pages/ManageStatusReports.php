<?php

namespace App\Filament\Resources\StatusReports\Pages;

use App\Filament\Resources\StatusReports\StatusReportResource;
use Filament\Resources\Pages\ManageRecords;

class ManageStatusReports extends ManageRecords
{
    protected static string $resource = StatusReportResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
