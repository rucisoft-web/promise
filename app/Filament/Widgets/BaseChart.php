<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Widgets\ChartWidget;

abstract class BaseChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected ?string $maxHeight = '300px';

    public const PALETTE = ['#0F5E9C', '#2A8C9E', '#D98E04', '#2E7D4F', '#D32F2F', '#6B4FA0', '#64748B', '#C2410C'];

    /** URL daftar resource dengan satu filter terpasang, mis. listUrl(ProjectResource::class, 'phase', 'pelaksanaan') */
    public static function listUrl(string $resource, ?string $filter = null, mixed $value = null): string
    {
        return $resource::getUrl('index', $filter ? ['filters' => [$filter => ['value' => $value]]] : []);
    }

    public static function projectUrl(int $id): string
    {
        return ProjectResource::getUrl('view', ['record' => $id]);
    }

    protected function legendBottom(array $extra = []): array
    {
        return array_replace_recursive([
            'plugins' => ['legend' => ['position' => 'bottom', 'labels' => ['usePointStyle' => true, 'boxWidth' => 8]]],
        ], $extra);
    }
}
