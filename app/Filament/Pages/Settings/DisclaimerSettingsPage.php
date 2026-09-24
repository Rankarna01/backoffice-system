<?php

namespace App\Filament\Pages\Settings;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class DisclaimerSettingsPage extends Page
{
    protected string $view = 'filament.pages.settings.disclaimer-settings-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;
    protected static \UnitEnum|string|null $navigationGroup = 'SETTINGS';

    protected static ?string $navigationLabel = 'Disclaimer';

    protected static ?int $navigationSort = 5;
    protected static ?string $slug = 'disclaimer';

    public function getTitle(): string
    {
        return 'Disclaimer';
    }
}
