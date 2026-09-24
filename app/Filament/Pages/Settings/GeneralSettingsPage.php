<?php

namespace App\Filament\Pages\Settings;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class GeneralSettingsPage extends Page
{
    protected string $view = 'filament.pages.settings.general-settings-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;
    protected static \UnitEnum|string|null $navigationGroup = 'SETTINGS';

    protected static ?string $navigationLabel = 'General';

    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'general';

    public function getTitle(): string
    {
        return 'General';
    }
}
