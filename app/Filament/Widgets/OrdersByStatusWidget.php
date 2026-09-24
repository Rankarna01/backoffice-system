<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class OrdersByStatusWidget extends Widget
{
    protected string $view = 'filament.widgets.orders-by-status-widget';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = [
        'default' => 12,
        'xl' => 4,
    ];

    protected static ?int $sort = 3;
}
