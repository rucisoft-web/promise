<?php

namespace App\Filament\Resources\Portfolios\Pages;

use App\Filament\Resources\Portfolios\PortfolioResource;
use Filament\Resources\Pages\ManageRecords;

class ManagePortfolios extends ManageRecords
{
    protected static string $resource = PortfolioResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
