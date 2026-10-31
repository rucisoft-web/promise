<?php

namespace App\Filament\Resources\Issues\Pages;

use App\Filament\Resources\Issues\IssueResource;
use Filament\Resources\Pages\ManageRecords;

class ManageIssues extends ManageRecords
{
    protected static string $resource = IssueResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
