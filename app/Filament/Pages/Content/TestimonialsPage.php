<?php

namespace App\Filament\Pages\Content;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class TestimonialsPage extends Page
{
    protected string $view = 'filament.pages.content.testimonials-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;
    protected static \UnitEnum|string|null $navigationGroup = 'CONTENT';

    protected static ?string $navigationLabel = 'Testimonials';

    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'testimonials';

    public function getTitle(): string
    {
        return 'Testimonials';
    }
}
