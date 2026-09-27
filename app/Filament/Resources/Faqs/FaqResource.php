<?php

namespace App\Filament\Resources\Faqs;

use App\Domain\Content\Models\Faq;
use App\Filament\Resources\Faqs\Pages\CreateFaq;
use App\Filament\Resources\Faqs\Pages\EditFaq;
use App\Filament\Resources\Faqs\Pages\ListFaqs;
use App\Filament\Resources\Faqs\Schemas\FaqForm;
use App\Filament\Resources\Faqs\Tables\FaqsTable;
use App\Filament\Resources\Faqs\Widgets\FaqStatsWidget;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static string|BackedEnum|null $navigationIcon = 'bx-help-circle';

    protected static \UnitEnum|string|null $navigationGroup = 'CONTENT';

    protected static ?string $navigationLabel = 'FAQ';

    protected static ?string $modelLabel = 'Pertanyaan FAQ';

    protected static ?string $pluralModelLabel = 'Tanya Jawab (FAQ)';

    protected static ?string $recordTitleAttribute = 'question';

    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        $publishedCount = Faq::where('is_published', true)->count();
        return $publishedCount > 0 ? (string) $publishedCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function form(Schema $schema): Schema
    {
        return FaqForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FaqsTable::configure($table);
    }

    public static function getWidgets(): array
    {
        return [
            FaqStatsWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFaqs::route('/'),
            'create' => CreateFaq::route('/create'),
            'edit' => EditFaq::route('/{record}/edit'),
        ];
    }
}
