<?php

namespace App\Filament\Resources\MarketNews;

use App\Domain\Market\Models\MarketNews;
use App\Filament\Resources\MarketNews\Pages\CreateMarketNews;
use App\Filament\Resources\MarketNews\Pages\EditMarketNews;
use App\Filament\Resources\MarketNews\Pages\ListMarketNews;
use App\Filament\Resources\MarketNews\Schemas\MarketNewsForm;
use App\Filament\Resources\MarketNews\Tables\MarketNewsTable;
use App\Filament\Resources\MarketNews\Widgets\MarketNewsStatsWidget;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MarketNewsResource extends Resource
{
    protected static ?string $model = MarketNews::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static \UnitEnum|string|null $navigationGroup = 'MARKET';

    protected static ?string $navigationLabel = 'News';

    protected static ?string $modelLabel = 'Berita Pasar Finansial';

    protected static ?string $pluralModelLabel = 'Market News';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return (string) MarketNews::where('status', 'published')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function form(Schema $schema): Schema
    {
        return MarketNewsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MarketNewsTable::configure($table);
    }

    public static function getWidgets(): array
    {
        return [
            MarketNewsStatsWidget::class,
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
            'index' => ListMarketNews::route('/'),
            'create' => CreateMarketNews::route('/create'),
            'edit' => EditMarketNews::route('/{record}/edit'),
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
