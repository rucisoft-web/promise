<?php

namespace App\Filament\Widgets;

use App\Services\PortfolioAnalytics as A;

class PortfolioSCurveChart extends BaseChart
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Kurva S portofolio (Earned Value, US$ juta kumulatif)';

    protected ?string $description = 'PV dari baseline WBS; EV dan AC dari laporan status bulanan.';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '340px';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        return static::curve(A::sCurve());
    }

    public static function curve(array $c): array
    {
        return [
            'labels' => $c['labels'],
            'datasets' => [
                ['label' => 'Planned Value (PV)', 'data' => $c['pv'], 'borderColor' => '#94A3B8', 'backgroundColor' => 'rgba(148,163,184,.15)', 'fill' => true, 'borderDash' => [6, 4], 'pointRadius' => 0, 'tension' => .35],
                ['label' => 'Earned Value (EV)', 'data' => $c['ev'], 'borderColor' => '#0F5E9C', 'backgroundColor' => 'rgba(15,94,156,.12)', 'fill' => false, 'pointRadius' => 2, 'tension' => .35, 'borderWidth' => 3],
                ['label' => 'Actual Cost (AC)', 'data' => $c['ac'], 'borderColor' => '#D98E04', 'backgroundColor' => 'rgba(217,142,4,.12)', 'fill' => false, 'pointRadius' => 2, 'tension' => .35, 'borderWidth' => 3],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return $this->legendBottom(['interaction' => ['mode' => 'index', 'intersect' => false], 'scales' => ['y' => ['beginAtZero' => true]]]);
    }
}
