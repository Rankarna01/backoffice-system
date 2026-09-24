<?php

namespace App\Filament\Pages\Market;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class NewsPage extends Page
{
    protected string $view = 'filament.pages.market.news-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;
    protected static \UnitEnum|string|null $navigationGroup = 'MARKET';

    protected static ?string $navigationLabel = 'News';

    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'news';

    public function getTitle(): string
    {
        return 'News';
    }
}
