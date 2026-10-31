<?php

namespace App\Filament\Resources\TaskDependencies\Pages;

use App\Filament\Resources\TaskDependencies\TaskDependencyResource;
use Filament\Resources\Pages\ManageRecords;

class ManageTaskDependencies extends ManageRecords
{
    protected static string $resource = TaskDependencyResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
