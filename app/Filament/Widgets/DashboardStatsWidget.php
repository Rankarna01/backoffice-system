<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DashboardStatsWidget extends Widget
{
    protected string $view = 'filament.widgets.dashboard-stats-widget';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;
}
