<?php

namespace App\Filament\Pages\UserManagement;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class MentorsPage extends Page
{
    protected string $view = 'filament.pages.user-management.mentors-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;
    protected static \UnitEnum|string|null $navigationGroup = 'USER MANAGEMENT';

    protected static ?string $navigationLabel = 'Mentors';

    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'mentors';

    public function getTitle(): string
    {
        return 'Mentors';
    }
}
