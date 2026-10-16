<?php

namespace App\Filament\Resources\LiveMarketCharts;

use App\Domain\Trading\Models\PaperTradingAccount;
use App\Filament\Resources\LiveMarketCharts\Pages\ListLiveMarketCharts;
use App\Filament\Resources\LiveMarketCharts\Tables\PaperTradingAccountsTable;
use App\Filament\Resources\LiveMarketCharts\Widgets\LiveMarketChartStatsWidget;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class LiveMarketChartResource extends Resource
{
    protected static ?string $model = PaperTradingAccount::class;

    protected static string|BackedEnum|null $navigationIcon = 'bx-line-chart';

    protected static \UnitEnum|string|null $navigationGroup = 'MARKET';

    protected static ?string $navigationLabel = 'Live Chart & Trading';

    protected static ?string $modelLabel = 'Akun Trading Member';

    protected static ?string $pluralModelLabel = 'Live Chart & Akun Trading';

    protected static ?int $navigationSort = 5;

    public static function getNavigationBadge(): ?string
    {
        return (string) PaperTradingAccount::where('status', 'active')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function table(Table $table): Table
    {
        return PaperTradingAccountsTable::configure($table);
    }

    public static function getWidgets(): array
    {
        return [
            LiveMarketChartStatsWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLiveMarketCharts::route('/'),
        ];
    }
}
