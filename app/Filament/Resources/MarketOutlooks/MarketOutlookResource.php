<?php

namespace App\Filament\Resources\MarketOutlooks;

use App\Domain\Market\Models\MarketOutlook;
use App\Filament\Resources\MarketOutlooks\Pages\CreateMarketOutlook;
use App\Filament\Resources\MarketOutlooks\Pages\EditMarketOutlook;
use App\Filament\Resources\MarketOutlooks\Pages\ListMarketOutlooks;
use App\Filament\Resources\MarketOutlooks\Schemas\MarketOutlookForm;
use App\Filament\Resources\MarketOutlooks\Tables\MarketOutlooksTable;
use App\Filament\Resources\MarketOutlooks\Widgets\MarketOutlookStatsWidget;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MarketOutlookResource extends Resource
{
    protected static ?string $model = MarketOutlook::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static \UnitEnum|string|null $navigationGroup = 'MARKET';

    protected static ?string $navigationLabel = 'Market Outlook';

    protected static ?string $modelLabel = 'Ulasan Market Outlook';

    protected static ?string $pluralModelLabel = 'Market Outlooks';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return (string) MarketOutlook::where('status', 'published')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function form(Schema $schema): Schema
    {
        return MarketOutlookForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MarketOutlooksTable::configure($table);
    }

    public static function getWidgets(): array
    {
        return [
            MarketOutlookStatsWidget::class,
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMarketOutlooks::route('/'),
            'create' => CreateMarketOutlook::route('/create'),
            'edit' => EditMarketOutlook::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
