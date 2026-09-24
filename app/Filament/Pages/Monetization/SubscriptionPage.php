<?php

namespace App\Filament\Pages\Monetization;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class SubscriptionPage extends Page
{
    protected string $view = 'filament.pages.monetization.subscription-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;
    protected static \UnitEnum|string|null $navigationGroup = 'MONETIZATION';

    protected static ?string $navigationLabel = 'Subscription';

    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'subscription';

    public function getTitle(): string
    {
        return 'Subscription';
    }
}
