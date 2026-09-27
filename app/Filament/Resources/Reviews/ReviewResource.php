<?php

namespace App\Filament\Resources\Reviews;

use App\Domain\Content\Enums\ReviewStatus;
use App\Domain\Content\Models\Review;
use App\Filament\Resources\Reviews\Pages\CreateReview;
use App\Filament\Resources\Reviews\Pages\EditReview;
use App\Filament\Resources\Reviews\Pages\ListReviews;
use App\Filament\Resources\Reviews\Schemas\ReviewForm;
use App\Filament\Resources\Reviews\Tables\ReviewsTable;
use App\Filament\Resources\Reviews\Widgets\ReviewStatsWidget;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string|BackedEnum|null $navigationIcon = 'bx-star';

    protected static \UnitEnum|string|null $navigationGroup = 'CONTENT';

    protected static ?string $navigationLabel = 'Reviews';

    protected static ?string $modelLabel = 'Ulasan Murid';

    protected static ?string $pluralModelLabel = 'Ulasan Kursus';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $pendingCount = Review::where('status', ReviewStatus::Pending)->count();
        return $pendingCount > 0 ? (string) $pendingCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return ReviewForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReviewsTable::configure($table);
    }

    public static function getWidgets(): array
    {
        return [
            ReviewStatsWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
            'create' => CreateReview::route('/create'),
            'edit' => EditReview::route('/{record}/edit'),
        ];
    }
}
