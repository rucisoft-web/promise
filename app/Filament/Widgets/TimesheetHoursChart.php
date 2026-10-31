<?php

namespace App\Filament\Widgets;

use App\Services\PortfolioAnalytics as A;

class TimesheetHoursChart extends BaseChart
{
    protected int|string|array $columnSpan = ['md' => 2, 'xl' => 2];

    protected static ?int $sort = 10;

    protected ?string $heading = 'Jam kerja tercatat (lembar waktu), 12 minggu terakhir';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $t = A::timesheetWeeks();
        $sets = [];
        $i = 0;
        foreach ($t['series'] as $label => $data) {
            $sets[] = ['label' => $label, 'data' => $data, 'backgroundColor' => self::PALETTE[$i++ % 8], 'borderRadius' => 3];
        }

        return ['labels' => $t['labels'], 'datasets' => $sets];
    }

    protected function getOptions(): array
    {
        return $this->legendBottom(['scales' => ['x' => ['stacked' => true], 'y' => ['stacked' => true]]]);
    }
}
