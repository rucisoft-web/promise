<?php

namespace App\Filament\Widgets;

use App\Services\PortfolioAnalytics as A;

class ContractStatusChart extends BaseChart
{
    protected static ?int $sort = 9;

    protected ?string $heading = 'Nilai kontrak per status pengadaan (US$ juta)';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $c = A::contractsByStatus();

        return [
            'labels' => array_keys($c),
            'datasets' => [['data' => array_values($c), 'backgroundColor' => array_slice(self::PALETTE, 0, count($c)), 'borderWidth' => 0, 'hoverOffset' => 8]],
        ];
    }

    protected function getOptions(): array
    {
        return $this->legendBottom(['cutout' => '55%', 'aspectRatio' => 1, 'scales' => ['x' => ['display' => false], 'y' => ['display' => false]]]);
    }
}
