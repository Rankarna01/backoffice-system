<?php

namespace App\Filament\Pages\Settings;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class EmailSettingsPage extends Page
{
    protected string $view = 'filament.pages.settings.email-settings-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;
    protected static \UnitEnum|string|null $navigationGroup = 'SETTINGS';

    protected static ?string $navigationLabel = 'Email';

    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'email';

    public function getTitle(): string
    {
        return 'Email';
    }
}
