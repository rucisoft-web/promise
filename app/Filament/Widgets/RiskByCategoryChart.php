<?php

namespace App\Filament\Widgets;

use App\Services\PortfolioAnalytics as A;

class RiskByCategoryChart extends BaseChart
{
    protected static ?int $sort = 8;

    protected ?string $heading = 'Risiko terbuka per kategori';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $r = A::riskByCategory();

        return [
            'labels' => array_keys($r),
            'datasets' => [
                ['label' => 'Skor tinggi (≥15)', 'data' => array_column($r, 'high'), 'backgroundColor' => '#D32F2F', 'borderRadius' => 4],
                ['label' => 'Lainnya', 'data' => array_map(fn ($v) => $v['count'] - $v['high'], array_values($r)), 'backgroundColor' => '#F2B8B8', 'borderRadius' => 4],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return $this->legendBottom(['indexAxis' => 'y', 'aspectRatio' => 0.95, 'scales' => ['x' => ['stacked' => true, 'ticks' => ['precision' => 0]], 'y' => ['stacked' => true]]]);
    }
}
