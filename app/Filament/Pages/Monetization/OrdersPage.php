<?php

namespace App\Filament\Pages\Monetization;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class OrdersPage extends Page
{
    protected string $view = 'filament.pages.monetization.orders-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;
    protected static \UnitEnum|string|null $navigationGroup = 'MONETIZATION';

    protected static ?string $navigationLabel = 'Orders';

    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'orders';

    public function getTitle(): string
    {
        return 'Orders';
    }
}
