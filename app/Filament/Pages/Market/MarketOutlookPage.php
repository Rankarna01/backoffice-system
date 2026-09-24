<?php

namespace App\Filament\Pages\Market;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class MarketOutlookPage extends Page
{
    protected string $view = 'filament.pages.market.market-outlook-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;
    protected static \UnitEnum|string|null $navigationGroup = 'MARKET';

    protected static ?string $navigationLabel = 'Market Outlook';

    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'market-outlook';

    public function getTitle(): string
    {
        return 'Market Outlook';
    }
}
