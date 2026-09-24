<?php

namespace App\Filament\Pages\Learning;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ModulesPage extends Page
{
    protected string $view = 'filament.pages.learning.modules-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;
    protected static \UnitEnum|string|null $navigationGroup = 'LEARNING';

    protected static ?string $navigationLabel = 'Modules';

    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'modules';

    public function getTitle(): string
    {
        return 'Modules';
    }
}
