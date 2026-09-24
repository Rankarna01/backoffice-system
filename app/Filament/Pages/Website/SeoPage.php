<?php

namespace App\Filament\Pages\Website;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class SeoPage extends Page
{
    protected string $view = 'filament.pages.website.seo-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;
    protected static \UnitEnum|string|null $navigationGroup = 'WEBSITE';

    protected static ?string $navigationLabel = 'SEO';

    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'seo';

    public function getTitle(): string
    {
        return 'SEO';
    }
}
