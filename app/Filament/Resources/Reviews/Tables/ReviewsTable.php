<?php

namespace App\Filament\Resources\Reviews\Tables;

use App\Domain\Content\Enums\ReviewStatus;
use App\Domain\Content\Models\Review;
use App\Domain\Learning\Models\Course;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Murid')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (Review $record): ?string => $record->user?->email),

                TextColumn::make('course.title')
                    ->label('Kursus')
                    ->badge()
                    ->color('info')
                    ->placeholder('Platform Umum')
                    ->limit(24)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->state(fn (Review $record): string => str_repeat('★', $record->rating) . str_repeat('☆', 5 - $record->rating))
                    ->color('warning')
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('comment')
                    ->label('Ulasan')
                    ->limit(50)
                    ->wrap()
                    ->description(fn (Review $record): ?string => $record->title ? "“{$record->title}”" : null),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state instanceof ReviewStatus ? $state->getColor() : 'gray')
                    ->formatStateUsing(fn ($state) => $state instanceof ReviewStatus ? $state->getLabel() : (string) $state)
                    ->icon(fn ($state) => $state instanceof ReviewStatus ? $state->getIcon() : null)
                    ->sortable(),

                ToggleColumn::make('is_featured')
                    ->label('Featured ⭐')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Moderasi')
                    ->options(ReviewStatus::options()),

                SelectFilter::make('rating')
                    ->label('Rating Bintang')
                    ->options([
                        5 => '★★★★★ (5 Bintang)',
                        4 => '★★★★☆ (4 Bintang)',
                        3 => '★★★☆☆ (3 Bintang)',
                        2 => '★★☆☆☆ (2 Bintang)',
                        1 => '★☆☆☆☆ (1 Bintang)',
                    ]),

                SelectFilter::make('course_id')
                    ->label('Materi Kursus')
                    ->options(fn () => Course::pluck('title', 'id')),

                TernaryFilter::make('is_featured')
                    ->label('Hanya Ulasan Pilihan (Featured)'),
            ])
            ->recordActions([
                Action::make('approve_review')
                    ->label('Setujui')
                    ->icon('bx-check-circle')
                    ->color('success')
                    ->visible(fn (Review $record): bool => $record->status === ReviewStatus::Pending)
                    ->action(function (Review $record) {
                        $record->update([
                            'status' => ReviewStatus::Approved,
                            'approved_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Ulasan Berhasil Disetujui')
                            ->success()
                            ->send();
                    }),

                Action::make('reject_review')
                    ->label('Tolak')
                    ->icon('bx-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Review $record): bool => $record->status !== ReviewStatus::Rejected)
                    ->action(function (Review $record) {
                        $record->update(['status' => ReviewStatus::Rejected]);

                        Notification::make()
                            ->title('Ulasan Ditolak & Disembunyikan')
                            ->warning()
                            ->send();
                    }),

                EditAction::make()->icon('bx-edit'),
                DeleteAction::make()->icon('bx-trash'),
            ]);
    }
}
