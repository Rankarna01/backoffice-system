<?php

namespace App\Filament\Pages\Website;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class BrandingPage extends Page
{
    protected string $view = 'filament.pages.website.branding-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaintBrush;
    protected static \UnitEnum|string|null $navigationGroup = 'WEBSITE';

    protected static ?string $navigationLabel = 'Branding';

    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'branding';

    public function getTitle(): string
    {
        return 'Branding';
    }
}
