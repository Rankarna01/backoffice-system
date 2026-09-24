<?php

namespace App\Filament\Pages\Monetization;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class PaymentsPage extends Page
{
    protected string $view = 'filament.pages.monetization.payments-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;
    protected static \UnitEnum|string|null $navigationGroup = 'MONETIZATION';

    protected static ?string $navigationLabel = 'Payments';

    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'payments';

    public function getTitle(): string
    {
        return 'Payments';
    }
}
