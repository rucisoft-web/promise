<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Projects\ProjectResource;
use App\Services\PortfolioAnalytics as A;
use App\Support\Pmbok;

class HealthByUnitChart extends BaseChart
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Kesehatan proyek per unit bisnis';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $h = A::healthByUnit();
        $urls = array_map(fn ($label) => self::listUrl(ProjectResource::class, 'business_unit', array_search($label, Pmbok::BUSINESS_UNITS)), array_keys($h));

        return [
            'labels' => array_keys($h),
            'datasets' => [
                ['label' => 'Hijau', 'data' => array_column($h, 'hijau'), 'backgroundColor' => '#2E7D4F', 'borderRadius' => 4, 'urls' => $urls],
                ['label' => 'Kuning', 'data' => array_column($h, 'kuning'), 'backgroundColor' => '#D98E04', 'borderRadius' => 4, 'urls' => $urls],
                ['label' => 'Merah', 'data' => array_column($h, 'merah'), 'backgroundColor' => '#D32F2F', 'borderRadius' => 4, 'urls' => $urls],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return $this->legendBottom(['indexAxis' => 'y', 'aspectRatio' => 1.25, 'scales' => ['x' => ['stacked' => true, 'ticks' => ['precision' => 0]], 'y' => ['stacked' => true]]]);
    }
}
