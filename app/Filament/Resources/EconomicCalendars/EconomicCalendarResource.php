<?php

namespace App\Filament\Resources\EconomicCalendars;

use App\Domain\Market\Models\WidgetConfig;
use App\Filament\Resources\EconomicCalendars\Pages\CreateEconomicCalendarEvent;
use App\Filament\Resources\EconomicCalendars\Pages\EditEconomicCalendarEvent;
use App\Filament\Resources\EconomicCalendars\Pages\ListEconomicCalendarEvents;
use App\Filament\Resources\EconomicCalendars\Schemas\WidgetConfigForm;
use App\Filament\Resources\EconomicCalendars\Tables\WidgetConfigsTable;
use App\Filament\Resources\EconomicCalendars\Widgets\EconomicCalendarStatsWidget;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EconomicCalendarResource extends Resource
{
    protected static ?string $model = WidgetConfig::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static \UnitEnum|string|null $navigationGroup = 'MARKET';

    protected static ?string $navigationLabel = 'Economic Calendar';

    protected static ?string $modelLabel = 'Konfigurasi Widget Kalender';

    protected static ?string $pluralModelLabel = 'TradingView Widget Configs';

    protected static ?string $recordTitleAttribute = 'widget_type';

    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        return (string) WidgetConfig::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Schema $schema): Schema
    {
        return WidgetConfigForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WidgetConfigsTable::configure($table);
    }

    public static function getWidgets(): array
    {
        return [
            EconomicCalendarStatsWidget::class,
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
            'index' => ListEconomicCalendarEvents::route('/'),
            'create' => CreateEconomicCalendarEvent::route('/create'),
            'edit' => EditEconomicCalendarEvent::route('/{record}/edit'),
        ];
    }
}
