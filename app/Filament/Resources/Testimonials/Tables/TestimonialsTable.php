<?php

namespace App\Filament\Resources\Testimonials\Tables;

use App\Domain\Content\Models\Testimonial;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl('https://ui-avatars.com/api/?background=0D8ABC&color=fff'),

                TextColumn::make('name')
                    ->label('Nama Murid')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (Testimonial $record): ?string => $record->role),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->state(fn (Testimonial $record): string => str_repeat('★', $record->rating))
                    ->color('warning')
                    ->weight('bold'),

                TextColumn::make('quote')
                    ->label('Testimoni')
                    ->limit(65)
                    ->wrap()
                    ->grow(true),

                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->alignCenter()
                    ->sortable(),

                ToggleColumn::make('is_featured')
                    ->label('Featured ⭐')
                    ->sortable(),

                ToggleColumn::make('is_published')
                    ->label('Tayang')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_published')
                    ->label('Hanya yang Tayang'),

                TernaryFilter::make('is_featured')
                    ->label('Hanya Featured Testimoni'),
            ])
            ->recordActions([
                EditAction::make()->icon('bx-edit'),
                DeleteAction::make()->icon('bx-trash'),
            ]);
    }
}
