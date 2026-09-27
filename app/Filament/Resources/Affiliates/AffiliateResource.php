<?php

namespace App\Filament\Resources\Affiliates;

use App\Domain\Billing\Enums\AffiliateStatus;
use App\Domain\Billing\Models\Affiliate;
use App\Filament\Resources\Affiliates\Pages\CreateAffiliate;
use App\Filament\Resources\Affiliates\Pages\EditAffiliate;
use App\Filament\Resources\Affiliates\Pages\ListAffiliates;
use App\Filament\Resources\Affiliates\RelationManagers\CommissionsRelationManager;
use App\Filament\Resources\Affiliates\RelationManagers\PayoutsRelationManager;
use App\Filament\Resources\Affiliates\Schemas\AffiliateForm;
use App\Filament\Resources\Affiliates\Tables\AffiliatesTable;
use App\Filament\Resources\Affiliates\Widgets\AffiliateStatsWidget;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class AffiliateResource extends Resource
{
    protected static ?string $model = Affiliate::class;

    protected static string|BackedEnum|null $navigationIcon = 'bx-share-alt';

    protected static \UnitEnum|string|null $navigationGroup = 'MONETIZATION';

    protected static ?string $navigationLabel = 'Affiliate';

    protected static ?string $modelLabel = 'Mitra Afiliasi';

    protected static ?string $pluralModelLabel = 'Program Afiliasi';

    protected static ?string $recordTitleAttribute = 'code';

    protected static ?int $navigationSort = 5;

    public static function getNavigationBadge(): ?string
    {
        $pendingCount = Affiliate::where('status', AffiliateStatus::Pending)->count();
        return $pendingCount > 0 ? (string) $pendingCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return AffiliateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AffiliatesTable::configure($table);
    }

    public static function getWidgets(): array
    {
        return [
            AffiliateStatsWidget::class,
        ];
    }

    public static function getRelations(): array
    {
        return [
            CommissionsRelationManager::class,
            PayoutsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAffiliates::route('/'),
            'create' => CreateAffiliate::route('/create'),
            'edit' => EditAffiliate::route('/{record}/edit'),
        ];
    }
}
