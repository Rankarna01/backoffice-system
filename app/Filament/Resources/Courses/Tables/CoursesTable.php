<?php

namespace App\Filament\Resources\Courses\Tables;

use App\Domain\Learning\Models\Course;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Kursus')
                    ->description(fn (Course $record): ?string => $record->subtitle)
                    ->searchable(['title', 'subtitle', 'slug'])
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('mentor.name')
                    ->label('Mentor')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Course $record): ?string => $record->mentor?->email),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('level')
                    ->label('Level')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'beginner' => 'success',
                        'intermediate' => 'warning',
                        'advanced' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('access_type')
                    ->label('Akses')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'free' => 'success',
                        'paid' => 'primary',
                        'subscription' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'free' => 'Free',
                        'paid' => 'Paid',
                        'subscription' => 'VIP Plan',
                        default => ucfirst($state),
                    }),

                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                TextColumn::make('students_count')
                    ->label('Murid')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('rating_avg')
                    ->label('Rating')
                    ->formatStateUsing(fn ($state, Course $record): string => (float) $state > 0 ? "★ {$state} ({$record->rating_count})" : '-')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'in_review' => 'warning',
                        'published' => 'success',
                        'unpublished' => 'danger',
                        'archived' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'in_review' => 'In Review',
                        'published' => 'Published',
                        'unpublished' => 'Unpublished',
                        'archived' => 'Archived',
                        default => ucfirst($state),
                    }),

                TextColumn::make('published_at')
                    ->label('Tayang')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Publikasi')
                    ->options([
                        'draft' => 'Draft',
                        'in_review' => 'In Review',
                        'published' => 'Published',
                        'unpublished' => 'Unpublished',
                        'archived' => 'Archived',
                    ]),

                SelectFilter::make('access_type')
                    ->label('Model Akses')
                    ->options([
                        'free' => 'Gratis',
                        'paid' => 'Sekali Bayar',
                        'subscription' => 'Langganan VIP',
                    ]),

                SelectFilter::make('level')
                    ->label('Level')
                    ->options([
                        'beginner' => 'Beginner',
                        'intermediate' => 'Intermediate',
                        'advanced' => 'Advanced',
                    ]),

                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name'),

                SelectFilter::make('mentor_id')
                    ->label('Mentor')
                    ->relationship('mentor', 'name'),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),

                    Action::make('publish')
                        ->label('Publish Kursus')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->visible(fn (Course $record): bool => $record->status !== 'published')
                        ->action(fn (Course $record) => $record->update([
                            'status' => 'published',
                            'published_at' => $record->published_at ?? now(),
                        ])),

                    Action::make('unpublish')
                        ->label('Tarik dari Publik')
                        ->icon('heroicon-m-eye-slash')
                        ->color('warning')
                        ->visible(fn (Course $record): bool => $record->status === 'published')
                        ->action(fn (Course $record) => $record->update([
                            'status' => 'unpublished',
                        ])),

                    Action::make('submit_review')
                        ->label('Ajukan Review')
                        ->icon('heroicon-m-paper-airplane')
                        ->color('info')
                        ->visible(fn (Course $record): bool => $record->status === 'draft')
                        ->action(fn (Course $record) => $record->update([
                            'status' => 'in_review',
                        ])),

                    Action::make('archive')
                        ->label('Arsipkan')
                        ->icon('heroicon-m-archive-box')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn (Course $record): bool => $record->status !== 'archived')
                        ->action(fn (Course $record) => $record->update([
                            'status' => 'archived',
                        ])),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
