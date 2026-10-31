<?php

namespace App\Filament\Pages;

use App\Filament\Widgets;
use Filament\Pages\Dashboard;

class Dasbor extends Dashboard
{
    protected static ?string $title = 'Dasbor Portofolio Proyek';

    protected static ?string $navigationLabel = 'Dasbor Portofolio';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';

    protected static string|\UnitEnum|null $navigationGroup = 'Umum';

    protected static ?int $navigationSort = 1;

    public function getSubheading(): ?string
    {
        return 'Status per '.\App\Services\PortfolioAnalytics::statusDate()->translatedFormat('d F Y').' · Earned Value Management sesuai PMBOK';
    }

    public function getWidgets(): array
    {
        return [
            Widgets\PortfolioStats::class,
            Widgets\PortfolioSCurveChart::class,
            Widgets\ProjectsByPhaseChart::class,
            Widgets\HealthByUnitChart::class,
            Widgets\SpiCpiChart::class,
            Widgets\BudgetVsEacChart::class,
            Widgets\RiskMatrixWidget::class,
            Widgets\RiskByCategoryChart::class,
            Widgets\TimesheetHoursChart::class,
            Widgets\ContractStatusChart::class,
            Widgets\UpcomingMilestones::class,
        ];
    }

    public function getColumns(): int|array
    {
        return ['md' => 2, 'xl' => 3];
    }
}
