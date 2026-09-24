<?php

namespace App\Filament\Pages\Learning;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class LearningMediaPage extends Page
{
    protected string $view = 'filament.pages.learning.learning-media-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;
    protected static \UnitEnum|string|null $navigationGroup = 'LEARNING';

    protected static ?string $navigationLabel = 'Media';

    protected static ?int $navigationSort = 5;
    protected static ?string $slug = 'learning-media';

    public function getTitle(): string
    {
        return 'Media';
    }
}
