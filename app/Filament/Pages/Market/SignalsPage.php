<?php

namespace App\Filament\Pages\Market;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class SignalsPage extends Page
{
    protected string $view = 'filament.pages.market.signals-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSignal;
    protected static \UnitEnum|string|null $navigationGroup = 'MARKET';

    protected static ?string $navigationLabel = 'Signals';

    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'signals';

    public function getTitle(): string
    {
        return 'Signals';
    }
}
