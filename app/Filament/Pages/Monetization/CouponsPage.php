<?php

namespace App\Filament\Pages\Monetization;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class CouponsPage extends Page
{
    protected string $view = 'filament.pages.monetization.coupons-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;
    protected static \UnitEnum|string|null $navigationGroup = 'MONETIZATION';

    protected static ?string $navigationLabel = 'Coupons';

    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'coupons';

    public function getTitle(): string
    {
        return 'Coupons';
    }
}
