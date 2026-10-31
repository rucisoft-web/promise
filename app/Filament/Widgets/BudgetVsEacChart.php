<?php

namespace App\Filament\Widgets;

use App\Services\PortfolioAnalytics as A;

class BudgetVsEacChart extends BaseChart
{
    protected static ?int $sort = 6;

    protected ?string $heading = 'Anggaran (BAC) vs perkiraan akhir (EAC) vs aktual (AC), US$ juta';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '360px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $ps = A::active()->sortByDesc(fn ($p) => $p->evm()['bac'])->values();
        $urls = $ps->map(fn ($p) => self::projectUrl($p->id))->all();
        $m = fn ($k) => $ps->map(fn ($p) => round($p->evm()[$k] / 1e6, 2))->all();

        return [
            'labels' => $ps->pluck('code')->all(),
            'datasets' => [
                ['label' => 'BAC', 'data' => $m('bac'), 'backgroundColor' => '#0F5E9C', 'borderRadius' => 4, 'urls' => $urls],
                ['label' => 'EAC', 'data' => $m('eac'), 'backgroundColor' => '#2A8C9E', 'borderRadius' => 4, 'urls' => $urls],
                ['label' => 'AC', 'data' => $m('ac'), 'backgroundColor' => '#D98E04', 'borderRadius' => 4, 'urls' => $urls],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return $this->legendBottom(['interaction' => ['mode' => 'index', 'intersect' => false]]);
    }
}
