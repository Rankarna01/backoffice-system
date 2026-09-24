<?php

namespace App\Filament\Pages\Learning;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class LessonsPage extends Page
{
    protected string $view = 'filament.pages.learning.lessons-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPlayCircle;
    protected static \UnitEnum|string|null $navigationGroup = 'LEARNING';

    protected static ?string $navigationLabel = 'Lessons';

    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'lessons';

    public function getTitle(): string
    {
        return 'Lessons';
    }
}
