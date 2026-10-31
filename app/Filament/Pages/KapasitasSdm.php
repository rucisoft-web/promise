<?php

namespace App\Filament\Pages;

use App\Services\PortfolioAnalytics;
use Filament\Pages\Page;

/** Peta panas kapasitas sumber daya: alokasi penugasan per bulan dibanding kapasitas (100%). */
class KapasitasSdm extends Page
{
    protected static ?string $title = 'Kapasitas & Beban Sumber Daya';

    protected static ?string $navigationLabel = 'Peta Kapasitas';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-table-cells';

    protected static string|\UnitEnum|null $navigationGroup = 'Sumber Daya Manusia';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'kapasitas';

    protected string $view = 'filament.pages.kapasitas-sdm';

    protected function getViewData(): array
    {
        return PortfolioAnalytics::capacity(12);
    }
}
