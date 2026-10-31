<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BudgetVsEacChart;
use App\Filament\Widgets\PortfolioSCurveChart;
use App\Filament\Widgets\SpiCpiChart;
use App\Services\PortfolioAnalytics;
use Filament\Pages\Page;

/** Manajemen Nilai Hasil (EVM) seluruh proyek: PV, EV, AC, SV, CV, SPI, CPI, EAC, ETC, VAC, TCPI. */
class KinerjaBiaya extends Page
{
    protected static ?string $title = 'Kinerja Biaya & Jadwal (Earned Value)';

    protected static ?string $navigationLabel = 'Earned Value (EVM)';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Biaya';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'earned-value';

    protected string $view = 'filament.pages.kinerja-biaya';

    protected function getHeaderWidgets(): array
    {
        return [PortfolioSCurveChart::class];
    }

    protected function getFooterWidgets(): array
    {
        return [BudgetVsEacChart::class, SpiCpiChart::class];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return ['md' => 2];
    }

    protected function getViewData(): array
    {
        return ['projects' => PortfolioAnalytics::projects(), 'totals' => PortfolioAnalytics::totals()];
    }
}
