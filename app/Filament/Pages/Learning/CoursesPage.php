<?php

namespace App\Filament\Pages\Learning;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class CoursesPage extends Page
{
    protected string $view = 'filament.pages.learning.courses-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;
    protected static \UnitEnum|string|null $navigationGroup = 'LEARNING';

    protected static ?string $navigationLabel = 'Courses';

    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'courses';

    public function getTitle(): string
    {
        return 'Courses';
    }
}
