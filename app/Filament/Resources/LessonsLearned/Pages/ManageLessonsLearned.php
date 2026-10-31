<?php

namespace App\Filament\Resources\LessonsLearned\Pages;

use App\Filament\Resources\LessonsLearned\LessonLearnedResource;
use Filament\Resources\Pages\ManageRecords;

class ManageLessonsLearned extends ManageRecords
{
    protected static string $resource = LessonLearnedResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
