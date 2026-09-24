<?php

namespace App\Filament\Pages\Website;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class LandingPageSettingsPage extends Page
{
    protected string $view = 'filament.pages.website.landing-page-settings-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;
    protected static \UnitEnum|string|null $navigationGroup = 'WEBSITE';

    protected static ?string $navigationLabel = 'Landing Page';

    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'landing-page';

    public function getTitle(): string
    {
        return 'Landing Page';
    }
}
