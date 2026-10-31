<?php

namespace App\Filament\Widgets;

use App\Models\Risk;
use Filament\Widgets\Widget;

class RiskMatrixWidget extends Widget
{
    protected int|string|array $columnSpan = ['md' => 2, 'xl' => 2];

    protected static ?int $sort = 7;

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.risk-matrix';

    protected function getViewData(): array
    {
        $risks = Risk::with('project')->whereIn('status', ['terbuka', 'dipantau', 'terjadi'])->get();

        return ['risks' => $risks, 'top' => $risks->sortByDesc(fn ($r) => $r->score)->take(5)];
    }
}
