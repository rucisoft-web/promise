<?php

namespace App\Filament\Widgets;

use App\Models\Project;

class ProjectCostByWbsChart extends BaseChart
{
    public ?Project $record = null;

    protected ?string $heading = 'PV, EV, AC per deliverable WBS level 1 (Rp miliar)';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        if (! $this->record) {
            return ['datasets' => []];
        }
        $items = $this->record->wbsItems()->with('children')->where('level', 1)->get();
        $date = $this->record->statusDate();
        $calc = function ($top, string $k) use ($date) {
            $leaves = $top->children->isEmpty() ? collect([$top]) : $top->children;

            return round($leaves->sum(fn ($i) => match ($k) {
                'pv' => $i->planned_cost * $i->plannedPercentAt($date) / 100,
                'ev' => $i->planned_cost * $i->percent_complete / 100,
                default => $i->actual_cost,
            }) / 1e9, 2);
        };

        return [
            'labels' => $items->map(fn ($i) => $i->code.' '.\Illuminate\Support\Str::limit($i->name, 22))->all(),
            'datasets' => [
                ['label' => 'PV', 'data' => $items->map(fn ($i) => $calc($i, 'pv'))->all(), 'backgroundColor' => '#94A3B8', 'borderRadius' => 4],
                ['label' => 'EV', 'data' => $items->map(fn ($i) => $calc($i, 'ev'))->all(), 'backgroundColor' => '#0F5E9C', 'borderRadius' => 4],
                ['label' => 'AC', 'data' => $items->map(fn ($i) => $calc($i, 'ac'))->all(), 'backgroundColor' => '#D98E04', 'borderRadius' => 4],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return $this->legendBottom(['indexAxis' => 'y']);
    }
}
