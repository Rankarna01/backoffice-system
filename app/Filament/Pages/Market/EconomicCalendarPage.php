<?php

namespace App\Filament\Pages\Market;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class EconomicCalendarPage extends Page
{
    protected string $view = 'filament.pages.market.economic-calendar-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;
    protected static \UnitEnum|string|null $navigationGroup = 'MARKET';

    protected static ?string $navigationLabel = 'Economic Calendar';

    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'economic-calendar';

    public function getTitle(): string
    {
        return 'Economic Calendar';
    }
}
