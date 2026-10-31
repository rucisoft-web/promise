<?php

namespace App\Filament\Resources\Assignments\Pages;

use App\Filament\Resources\Assignments\AssignmentResource;
use Filament\Resources\Pages\ManageRecords;

class ManageAssignments extends ManageRecords
{
    protected static string $resource = AssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
