<?php

namespace App\Filament\Resources\Lessons\Tables;

use App\Domain\Learning\Models\Lesson;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LessonsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('primary')
                    ->extraHeaderAttributes(['style' => 'min-width: 70px;'])
                    ->extraCellAttributes(['style' => 'min-width: 70px;']),

                TextColumn::make('title')
                    ->label('Judul Pelajaran')
                    ->searchable(['title', 'slug', 'content'])
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->grow(true)
                    ->description(fn (Lesson $record): ?string => $record->video_provider ? "Provider: " . ucfirst($record->video_provider) : null)
                    ->extraHeaderAttributes(['style' => 'min-width: 320px; max-width: 500px;'])
                    ->extraCellAttributes(['style' => 'min-width: 320px; max-width: 500px;']),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'video' => 'info',
                        'article' => 'warning',
                        'file' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): ?string => match ($state) {
                        'video' => 'heroicon-m-play-circle',
                        'article' => 'heroicon-m-document-text',
                        'file' => 'heroicon-m-folder-arrow-down',
                        default => null,
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'video' => 'Video',
                        'article' => 'Artikel',
                        'file' => 'File / Tugas',
                        default => ucfirst($state),
                    })
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 110px;'])
                    ->extraCellAttributes(['style' => 'min-width: 110px;']),

                TextColumn::make('module.title')
                    ->label('Bab / Modul')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->wrap()
                    ->extraHeaderAttributes(['style' => 'min-width: 220px; max-width: 340px;'])
                    ->extraCellAttributes(['style' => 'min-width: 220px; max-width: 340px;']),

                TextColumn::make('course.title')
                    ->label('Kursus')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->wrap()
                    ->extraHeaderAttributes(['style' => 'min-width: 220px; max-width: 340px;'])
                    ->extraCellAttributes(['style' => 'min-width: 220px; max-width: 340px;']),

                TextColumn::make('duration_formatted')
                    ->label('Durasi')
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 90px;'])
                    ->extraCellAttributes(['style' => 'min-width: 90px;']),

                TextColumn::make('is_preview')
                    ->label('Preview')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Free Preview' : 'Terkunci')
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 120px;'])
                    ->extraCellAttributes(['style' => 'min-width: 120px;']),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                        'archived' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'published' => 'Published',
                        'draft' => 'Draft',
                        'archived' => 'Archived',
                        default => ucfirst($state),
                    })
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 110px;'])
                    ->extraCellAttributes(['style' => 'min-width: 110px;']),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('course_id')
                    ->label('Filter Kursus')
                    ->relationship('course', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('module_id')
                    ->label('Filter Bab / Modul')
                    ->relationship('module', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('type')
                    ->label('Tipe Format')
                    ->options([
                        'video' => 'Video Pembelajaran',
                        'article' => 'Artikel Teks',
                        'file' => 'File / Tugas',
                    ]),

                SelectFilter::make('status')
                    ->label('Status Publikasi')
                    ->options([
                        'published' => 'Published',
                        'draft' => 'Draft',
                        'archived' => 'Archived',
                    ]),

                Filter::make('is_preview')
                    ->label('Hanya Free Preview')
                    ->query(fn (Builder $query): Builder => $query->where('is_preview', true)),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),

                    Action::make('toggle_status')
                        ->label(fn (Lesson $record): string => $record->status === 'published' ? 'Ubah ke Draft' : 'Publikasikan Materi')
                        ->icon(fn (Lesson $record): string => $record->status === 'published' ? 'heroicon-m-eye-slash' : 'heroicon-m-check-circle')
                        ->color(fn (Lesson $record): string => $record->status === 'published' ? 'warning' : 'success')
                        ->action(function (Lesson $record) {
                            $newStatus = $record->status === 'published' ? 'draft' : 'published';
                            $record->update(['status' => $newStatus]);
                            Notification::make()
                                ->title($newStatus === 'published' ? 'Pelajaran dipublikasikan' : 'Pelajaran dialihkan ke draft')
                                ->success()
                                ->send();
                        }),

                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
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
