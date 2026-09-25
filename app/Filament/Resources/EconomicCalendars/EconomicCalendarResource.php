<?php

namespace App\Filament\Resources\EconomicCalendars;

use App\Domain\Market\Models\EconomicCalendarEvent;
use App\Filament\Resources\EconomicCalendars\Pages\CreateEconomicCalendarEvent;
use App\Filament\Resources\EconomicCalendars\Pages\EditEconomicCalendarEvent;
use App\Filament\Resources\EconomicCalendars\Pages\ListEconomicCalendarEvents;
use App\Filament\Resources\EconomicCalendars\Schemas\EconomicCalendarEventForm;
use App\Filament\Resources\EconomicCalendars\Tables\EconomicCalendarEventsTable;
use App\Filament\Resources\EconomicCalendars\Widgets\EconomicCalendarStatsWidget;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EconomicCalendarResource extends Resource
{
    protected static ?string $model = EconomicCalendarEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static \UnitEnum|string|null $navigationGroup = 'MARKET';

    protected static ?string $navigationLabel = 'Economic Calendar';

    protected static ?string $modelLabel = 'Event Kalender Ekonomi';

    protected static ?string $pluralModelLabel = 'Economic Calendar';

    protected static ?string $recordTitleAttribute = 'event_name';

    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        return (string) EconomicCalendarEvent::where('impact_level', 'high')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return EconomicCalendarEventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EconomicCalendarEventsTable::configure($table);
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
