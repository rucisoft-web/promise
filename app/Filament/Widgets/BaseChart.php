<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

abstract class BaseChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected ?string $maxHeight = '300px';

    public const PALETTE = ['#0F5E9C', '#2A8C9E', '#D98E04', '#2E7D4F', '#D32F2F', '#6B4FA0', '#64748B', '#C2410C'];

    protected function legendBottom(array $extra = []): array
    {
        return array_replace_recursive([
            'plugins' => ['legend' => ['position' => 'bottom', 'labels' => ['usePointStyle' => true, 'boxWidth' => 8]]],
        ], $extra);
    }
}
