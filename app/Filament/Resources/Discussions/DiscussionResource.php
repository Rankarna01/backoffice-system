<?php

namespace App\Filament\Resources\Discussions;

use App\Domain\Community\Enums\ModerationStatus;
use App\Domain\Community\Models\Discussion;
use App\Filament\Resources\Discussions\Pages\CreateDiscussion;
use App\Filament\Resources\Discussions\Pages\EditDiscussion;
use App\Filament\Resources\Discussions\Pages\ListDiscussions;
use App\Filament\Resources\Discussions\RelationManagers\RepliesRelationManager;
use App\Filament\Resources\Discussions\Schemas\DiscussionForm;
use App\Filament\Resources\Discussions\Tables\DiscussionsTable;
use App\Filament\Resources\Discussions\Widgets\DiscussionStatsWidget;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DiscussionResource extends Resource
{
    protected static ?string $model = Discussion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static \UnitEnum|string|null $navigationGroup = 'COMMUNITY';

    protected static ?string $navigationLabel = 'Discussions';

    protected static ?string $modelLabel = 'Diskusi Komunitas';

    protected static ?string $pluralModelLabel = 'Forum Diskusi';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $flaggedCount = Discussion::whereIn('moderation_status', [
            ModerationStatus::PendingReview->value,
            ModerationStatus::Flagged->value,
        ])->count();

        return $flaggedCount > 0 ? (string) $flaggedCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return DiscussionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DiscussionsTable::configure($table);
    }

    public static function getWidgets(): array
    {
        return [
            DiscussionStatsWidget::class,
        ];
    }

    public static function getRelations(): array
    {
        return [
            RepliesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDiscussions::route('/'),
            'create' => CreateDiscussion::route('/create'),
            'edit' => EditDiscussion::route('/{record}/edit'),
        ];
    }
}
