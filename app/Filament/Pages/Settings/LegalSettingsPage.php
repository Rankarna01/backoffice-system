<?php

namespace App\Filament\Pages\Settings;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class LegalSettingsPage extends Page
{
    protected string $view = 'filament.pages.settings.legal-settings-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;
    protected static \UnitEnum|string|null $navigationGroup = 'SETTINGS';

    protected static ?string $navigationLabel = 'Legal';

    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'legal';

    public function getTitle(): string
    {
        return 'Legal';
    }
}
