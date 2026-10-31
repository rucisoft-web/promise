<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Risks\RiskResource;
use App\Services\PortfolioAnalytics as A;
use App\Support\Pmbok;

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
        $urls = array_map(fn ($label) => self::listUrl(RiskResource::class, 'category', array_search($label, Pmbok::RISK_CATEGORIES)), array_keys($r));

        return [
            'labels' => array_keys($r),
            'datasets' => [
                ['label' => 'Skor tinggi (≥15)', 'data' => array_column($r, 'high'), 'backgroundColor' => '#D32F2F', 'borderRadius' => 4, 'urls' => $urls],
                ['label' => 'Lainnya', 'data' => array_map(fn ($v) => $v['count'] - $v['high'], array_values($r)), 'backgroundColor' => '#F2B8B8', 'borderRadius' => 4, 'urls' => $urls],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return $this->legendBottom(['indexAxis' => 'y', 'aspectRatio' => 0.95, 'scales' => ['x' => ['stacked' => true, 'ticks' => ['precision' => 0]], 'y' => ['stacked' => true]]]);
    }
}
