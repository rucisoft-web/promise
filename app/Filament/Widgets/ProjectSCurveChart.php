<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Services\PortfolioAnalytics as A;

class ProjectSCurveChart extends BaseChart
{
    public ?Project $record = null;

    protected ?string $heading = 'Kurva S proyek (Rp miliar kumulatif)';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        return $this->record ? PortfolioSCurveChart::curve(A::sCurve($this->record)) : ['datasets' => []];
    }

    protected function getOptions(): array
    {
        return $this->legendBottom(['interaction' => ['mode' => 'index', 'intersect' => false], 'scales' => ['y' => ['beginAtZero' => true]]]);
    }
}
