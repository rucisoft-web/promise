<?php

namespace App\Filament\Widgets;

use App\Services\PortfolioAnalytics as A;
use App\Support\Pmbok;

class ProjectsByPhaseChart extends BaseChart
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Proyek per kelompok proses';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        return [
            'labels' => array_values(Pmbok::PHASES),
            'datasets' => [['data' => array_values(A::byPhase()), 'backgroundColor' => ['#94A3B8', '#2A8C9E', '#0F5E9C', '#D98E04', '#2E7D4F'], 'borderWidth' => 0, 'hoverOffset' => 8]],
        ];
    }

    protected function getOptions(): array
    {
        return $this->legendBottom(['cutout' => '62%', 'aspectRatio' => 1.25, 'scales' => ['x' => ['display' => false], 'y' => ['display' => false]]]);
    }
}
