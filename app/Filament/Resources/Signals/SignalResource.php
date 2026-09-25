<?php

namespace App\Filament\Resources\Signals;

use App\Domain\Market\Models\Signal;
use App\Filament\Resources\Signals\Pages\CreateSignal;
use App\Filament\Resources\Signals\Pages\EditSignal;
use App\Filament\Resources\Signals\Pages\ListSignals;
use App\Filament\Resources\Signals\Schemas\SignalForm;
use App\Filament\Resources\Signals\Tables\SignalsTable;
use App\Filament\Resources\Signals\Widgets\SignalStatsWidget;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SignalResource extends Resource
{
    protected static ?string $model = Signal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSignal;

    protected static \UnitEnum|string|null $navigationGroup = 'MARKET';

    protected static ?string $navigationLabel = 'Signals';

    protected static ?string $modelLabel = 'Sinyal Trading';

    protected static ?string $pluralModelLabel = 'Sinyal Trading';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) Signal::whereIn('status', ['active', 'pending'])->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Schema $schema): Schema
    {
        return SignalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SignalsTable::configure($table);
    }

    public static function getWidgets(): array
    {
        return [
            SignalStatsWidget::class,
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
            'index' => ListSignals::route('/'),
            'create' => CreateSignal::route('/create'),
            'edit' => EditSignal::route('/{record}/edit'),
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
