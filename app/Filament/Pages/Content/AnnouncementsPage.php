<?php

namespace App\Filament\Pages\Content;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class AnnouncementsPage extends Page
{
    protected string $view = 'filament.pages.content.announcements-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;
    protected static \UnitEnum|string|null $navigationGroup = 'CONTENT';

    protected static ?string $navigationLabel = 'Announcements';

    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'announcements';

    public function getTitle(): string
    {
        return 'Announcements';
    }
}
