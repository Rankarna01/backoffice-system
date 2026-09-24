<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class LatestOrdersWidget extends Widget
{
    protected string $view = 'filament.widgets.latest-orders-widget';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = [
        'default' => 12,
        'xl' => 8,
    ];

    protected static ?int $sort = 4;
}
