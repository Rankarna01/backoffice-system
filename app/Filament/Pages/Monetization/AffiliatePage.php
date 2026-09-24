<?php

namespace App\Filament\Pages\Monetization;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class AffiliatePage extends Page
{
    protected string $view = 'filament.pages.monetization.affiliate-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;
    protected static \UnitEnum|string|null $navigationGroup = 'MONETIZATION';

    protected static ?string $navigationLabel = 'Affiliate';

    protected static ?int $navigationSort = 5;
    protected static ?string $slug = 'affiliate';

    public function getTitle(): string
    {
        return 'Affiliate';
    }
}
