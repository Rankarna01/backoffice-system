<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class AnalyticsPage extends Page
{
    protected string $view = 'filament.pages.analytics-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static ?string $navigationLabel = 'Analytics';

    protected static ?int $navigationSort = 85;
    protected static ?string $slug = 'analytics';

    public function getTitle(): string
    {
        return 'Analytics';
    }
}
