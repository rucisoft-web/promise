<?php

namespace App\Filament\Widgets;

use App\Services\PortfolioAnalytics as A;
use Filament\Widgets\Widget;

class UpcomingMilestones extends Widget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 11;

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.upcoming-milestones';

    protected function getViewData(): array
    {
        return ['items' => A::upcomingMilestones(), 'statusDate' => A::statusDate()];
    }
}
