<?php

namespace App\Filament\Pages\Community;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class DiscussionPage extends Page
{
    protected string $view = 'filament.pages.community.discussion-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;
    protected static \UnitEnum|string|null $navigationGroup = 'COMMUNITY';

    protected static ?string $navigationLabel = 'Discussion';

    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'discussion';

    public function getTitle(): string
    {
        return 'Discussion';
    }
}
