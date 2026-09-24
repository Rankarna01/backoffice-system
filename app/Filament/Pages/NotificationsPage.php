<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class NotificationsPage extends Page
{
    protected string $view = 'filament.pages.notifications-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    protected static ?string $navigationLabel = 'Notifications';

    protected static ?int $navigationSort = 80;
    protected static ?string $slug = 'notifications';

    public function getTitle(): string
    {
        return 'Notifications';
    }
}
