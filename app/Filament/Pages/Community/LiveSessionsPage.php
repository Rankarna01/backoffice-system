<?php

namespace App\Filament\Pages\Community;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class LiveSessionsPage extends Page
{
    protected string $view = 'filament.pages.community.live-sessions-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedVideoCamera;
    protected static \UnitEnum|string|null $navigationGroup = 'COMMUNITY';

    protected static ?string $navigationLabel = 'Live Sessions';

    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'live-sessions';

    public function getTitle(): string
    {
        return 'Live Sessions';
    }
}
