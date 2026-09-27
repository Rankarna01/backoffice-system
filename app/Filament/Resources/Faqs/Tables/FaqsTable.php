<?php

namespace App\Filament\Resources\Faqs\Tables;

use App\Domain\Content\Models\Faq;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FaqsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->columns([
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('question')
                    ->label('Pertanyaan FAQ')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->wrap()
                    ->grow(true)
                    ->description(fn (Faq $record): string => Str($record->answer)->limit(70)->stripTags()),

                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->alignCenter()
                    ->sortable(),

                ToggleColumn::make('is_published')
                    ->label('Tayang')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'General' => 'Umum & Platform',
                        'Kursus & Materi' => 'Kursus & Pembelajaran',
                        'Sinyal & Indikator' => 'Sinyal Trading & Indikator',
                        'Langganan & Billing' => 'Langganan & Pembayaran',
                        'Komunitas' => 'Komunitas & Live Session',
                    ]),

                TernaryFilter::make('is_published')
                    ->label('Hanya FAQ Tayang'),
            ])
            ->recordActions([
                EditAction::make()->icon('bx-edit'),
                DeleteAction::make()->icon('bx-trash'),
            ]);
    }
}
