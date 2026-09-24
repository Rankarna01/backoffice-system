<?php

namespace App\Filament\Pages\Content;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class FaqPage extends Page
{
    protected string $view = 'filament.pages.content.faq-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;
    protected static \UnitEnum|string|null $navigationGroup = 'CONTENT';

    protected static ?string $navigationLabel = 'FAQ';

    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'faq';

    public function getTitle(): string
    {
        return 'FAQ';
    }
}
