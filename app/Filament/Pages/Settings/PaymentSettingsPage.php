<?php

namespace App\Filament\Pages\Settings;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class PaymentSettingsPage extends Page
{
    protected string $view = 'filament.pages.settings.payment-settings-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;
    protected static \UnitEnum|string|null $navigationGroup = 'SETTINGS';

    protected static ?string $navigationLabel = 'Payment';

    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'payment';

    public function getTitle(): string
    {
        return 'Payment';
    }
}
