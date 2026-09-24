<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class UpcomingSessionsAndApprovalWidget extends Widget
{
    protected string $view = 'filament.widgets.upcoming-sessions-and-approval-widget';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = [
        'default' => 12,
        'xl' => 4,
    ];

    protected static ?int $sort = 5;
}
