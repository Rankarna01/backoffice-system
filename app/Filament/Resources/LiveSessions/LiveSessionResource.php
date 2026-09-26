<?php

namespace App\Filament\Resources\LiveSessions;

use App\Domain\Community\Enums\LiveSessionStatus;
use App\Domain\Community\Models\LiveSession;
use App\Filament\Resources\LiveSessions\Pages\CreateLiveSession;
use App\Filament\Resources\LiveSessions\Pages\EditLiveSession;
use App\Filament\Resources\LiveSessions\Pages\ListLiveSessions;
use App\Filament\Resources\LiveSessions\RelationManagers\RegistrationsRelationManager;
use App\Filament\Resources\LiveSessions\Schemas\LiveSessionForm;
use App\Filament\Resources\LiveSessions\Tables\LiveSessionsTable;
use App\Filament\Resources\LiveSessions\Widgets\LiveSessionStatsWidget;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LiveSessionResource extends Resource
{
    protected static ?string $model = LiveSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedVideoCamera;

    protected static \UnitEnum|string|null $navigationGroup = 'COMMUNITY';

    protected static ?string $navigationLabel = 'Live Sessions';

    protected static ?string $modelLabel = 'Sesi Live';

    protected static ?string $pluralModelLabel = 'Live Sessions & Webinar';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $liveCount = LiveSession::where('status', LiveSessionStatus::Live)->count();
        if ($liveCount > 0) {
            return "LIVE ({$liveCount})";
        }

        $upcomingCount = LiveSession::where('status', LiveSessionStatus::Upcoming)->count();
        return $upcomingCount > 0 ? (string) $upcomingCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $liveCount = LiveSession::where('status', LiveSessionStatus::Live)->count();
        return $liveCount > 0 ? 'danger' : 'info';
    }

    public static function form(Schema $schema): Schema
    {
        return LiveSessionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LiveSessionsTable::configure($table);
    }

    public static function getWidgets(): array
    {
        return [
            LiveSessionStatsWidget::class,
        ];
    }

    public static function getRelations(): array
    {
        return [
            RegistrationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLiveSessions::route('/'),
            'create' => CreateLiveSession::route('/create'),
            'edit' => EditLiveSession::route('/{record}/edit'),
        ];
    }
}
