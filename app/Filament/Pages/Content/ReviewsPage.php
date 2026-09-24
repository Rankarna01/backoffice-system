<?php

namespace App\Filament\Pages\Content;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ReviewsPage extends Page
{
    protected string $view = 'filament.pages.content.reviews-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;
    protected static \UnitEnum|string|null $navigationGroup = 'CONTENT';

    protected static ?string $navigationLabel = 'Reviews';

    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'reviews';

    public function getTitle(): string
    {
        return 'Reviews';
    }
}
