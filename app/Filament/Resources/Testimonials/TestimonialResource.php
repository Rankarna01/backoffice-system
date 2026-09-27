<?php

namespace App\Filament\Resources\Testimonials;

use App\Domain\Content\Models\Testimonial;
use App\Filament\Resources\Testimonials\Pages\CreateTestimonial;
use App\Filament\Resources\Testimonials\Pages\EditTestimonial;
use App\Filament\Resources\Testimonials\Pages\ListTestimonials;
use App\Filament\Resources\Testimonials\Schemas\TestimonialForm;
use App\Filament\Resources\Testimonials\Tables\TestimonialsTable;
use App\Filament\Resources\Testimonials\Widgets\TestimonialStatsWidget;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static string|BackedEnum|null $navigationIcon = 'bx-chat';

    protected static \UnitEnum|string|null $navigationGroup = 'CONTENT';

    protected static ?string $navigationLabel = 'Testimonials';

    protected static ?string $modelLabel = 'Testimoni Landing';

    protected static ?string $pluralModelLabel = 'Testimoni & Social Proof';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        $publishedCount = Testimonial::where('is_published', true)->count();
        return $publishedCount > 0 ? (string) $publishedCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function form(Schema $schema): Schema
    {
        return TestimonialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TestimonialsTable::configure($table);
    }

    public static function getWidgets(): array
    {
        return [
            TestimonialStatsWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTestimonials::route('/'),
            'create' => CreateTestimonial::route('/create'),
            'edit' => EditTestimonial::route('/{record}/edit'),
        ];
    }
}
