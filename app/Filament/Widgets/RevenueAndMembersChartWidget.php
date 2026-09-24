<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class RevenueAndMembersChartWidget extends Widget
{
    protected string $view = 'filament.widgets.revenue-and-members-chart-widget';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = [
        'default' => 12,
        'xl' => 8,
    ];

    protected static ?int $sort = 2;
}
