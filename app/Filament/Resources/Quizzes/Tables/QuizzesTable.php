<?php

namespace App\Filament\Resources\Quizzes\Tables;

use App\Domain\Learning\Models\Quiz;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuizzesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Kuis Evaluasi')
                    ->searchable(['title', 'instructions'])
                    ->sortable()
                    ->weight('bold')
                    ->grow(true)
                    ->wrap()
                    ->description(fn (Quiz $record): ?string => $record->instructions)
                    ->extraHeaderAttributes(['style' => 'min-width: 320px; max-width: 500px;'])
                    ->extraCellAttributes(['style' => 'min-width: 320px; max-width: 500px;']),

                TextColumn::make('course.title')
                    ->label('Kursus Induk')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->wrap()
                    ->extraHeaderAttributes(['style' => 'min-width: 220px; max-width: 340px;'])
                    ->extraCellAttributes(['style' => 'min-width: 220px; max-width: 340px;']),

                TextColumn::make('module.title')
                    ->label('Bab / Modul')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state ? 'gray' : 'warning')
                    ->formatStateUsing(fn ($state) => $state ?? 'Ujian Akhir Kursus')
                    ->wrap()
                    ->extraHeaderAttributes(['style' => 'min-width: 200px; max-width: 320px;'])
                    ->extraCellAttributes(['style' => 'min-width: 200px; max-width: 320px;']),

                TextColumn::make('questions_count')
                    ->counts('questions')
                    ->label('Soal')
                    ->badge()
                    ->color('primary')
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 80px;'])
                    ->extraCellAttributes(['style' => 'min-width: 80px;']),

                TextColumn::make('passing_score')
                    ->label('Passing Score')
                    ->formatStateUsing(fn ($state): string => "{$state}%")
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 120px;'])
                    ->extraCellAttributes(['style' => 'min-width: 120px;']),

                TextColumn::make('time_limit_minutes')
                    ->label('Batas Waktu')
                    ->formatStateUsing(fn ($state): string => $state ? "{$state} mnt" : 'Tanpa Batas')
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 110px;'])
                    ->extraCellAttributes(['style' => 'min-width: 110px;']),

                TextColumn::make('is_required')
                    ->label('Syarat Kelulusan')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Wajib Lulus' : 'Opsional')
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

                SelectFilter::make('status')
                    ->label('Status Publikasi')
                    ->options([
                        'published' => 'Published',
                        'draft' => 'Draft',
                        'archived' => 'Archived',
                    ]),

                Filter::make('is_required')
                    ->label('Hanya Kuis Wajib Sertifikat')
                    ->query(fn (Builder $query): Builder => $query->where('is_required', true)),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),

                    Action::make('toggle_status')
                        ->label(fn (Quiz $record): string => $record->status === 'published' ? 'Ubah ke Draft' : 'Publikasikan Kuis')
                        ->icon(fn (Quiz $record): string => $record->status === 'published' ? 'heroicon-m-eye-slash' : 'heroicon-m-check-circle')
                        ->color(fn (Quiz $record): string => $record->status === 'published' ? 'warning' : 'success')
                        ->action(function (Quiz $record) {
                            $newStatus = $record->status === 'published' ? 'draft' : 'published';
                            $record->update(['status' => $newStatus]);
                            Notification::make()
                                ->title($newStatus === 'published' ? 'Kuis dipublikasikan' : 'Kuis dialihkan ke draft')
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
