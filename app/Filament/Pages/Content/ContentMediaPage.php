<?php

namespace App\Filament\Pages\Content;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ContentMediaPage extends Page
{
    protected string $view = 'filament.pages.content.content-media-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;
    protected static \UnitEnum|string|null $navigationGroup = 'CONTENT';

    protected static ?string $navigationLabel = 'Media';

    protected static ?int $navigationSort = 5;
    protected static ?string $slug = 'content-media';

    public function getTitle(): string
    {
        return 'Media';
    }
}
