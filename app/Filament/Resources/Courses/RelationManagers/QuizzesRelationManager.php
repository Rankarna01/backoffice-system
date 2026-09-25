<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Domain\Learning\Models\Quiz;
use App\Filament\Resources\Quizzes\QuizResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuizzesRelationManager extends RelationManager
{
    protected static string $relationship = 'quizzes';

    protected static ?string $title = 'Kuis & Evaluasi Pemahaman';

    protected static ?string $modelLabel = 'Kuis / Evaluasi';

    protected static ?string $pluralModelLabel = 'Kuis-Kuis Kursus';

    public function form(Schema $schema): Schema
    {
        return QuizResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Kuis')
                    ->description(fn (Quiz $record): ?string => $record->instructions)
                    ->searchable(['title', 'instructions'])
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->grow(true)
                    ->url(fn (Quiz $record): string => QuizResource::getUrl('edit', ['record' => $record]))
                    ->extraHeaderAttributes(['style' => 'min-width: 280px;'])
                    ->extraCellAttributes(['style' => 'min-width: 280px;']),

                TextColumn::make('module.title')
                    ->label('Modul Terkait')
                    ->placeholder('Kuis Akhir Kursus (Global)')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->extraHeaderAttributes(['style' => 'min-width: 180px;'])
                    ->extraCellAttributes(['style' => 'min-width: 180px;']),

                TextColumn::make('questions_count')
                    ->label('Soal')
                    ->counts('questions')
                    ->badge()
                    ->color('primary')
                    ->alignCenter()
                    ->suffix(' butir')
                    ->extraHeaderAttributes(['style' => 'min-width: 100px;'])
                    ->extraCellAttributes(['style' => 'min-width: 100px;']),

                TextColumn::make('passing_score')
                    ->label('Kelulusan')
                    ->formatStateUsing(fn (int $state): string => "{$state}%")
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        default => 'danger',
                    })
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 100px;'])
                    ->extraCellAttributes(['style' => 'min-width: 100px;']),

                TextColumn::make('time_limit_minutes')
                    ->label('Batas Waktu')
                    ->formatStateUsing(fn (?int $state): string => $state ? "{$state} mnt" : 'Fleksibel')
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 110px;'])
                    ->extraCellAttributes(['style' => 'min-width: 110px;']),

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
                    ->extraHeaderAttributes(['style' => 'min-width: 100px;'])
                    ->extraCellAttributes(['style' => 'min-width: 100px;']),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'published' => 'Published',
                        'draft' => 'Draft',
                        'archived' => 'Archived',
                    ]),

                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Buat Kuis Baru')
                    ->url(fn (): string => QuizResource::getUrl('create', [
                        'course_id' => $this->getOwnerRecord()->getKey(),
                    ]))
                    ->modal(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->url(fn (Quiz $record): string => QuizResource::getUrl('edit', ['record' => $record]))
                        ->modal(false),

                    Action::make('toggle_publish')
                        ->label(fn (Quiz $record): string => $record->status === 'published' ? 'Ubah ke Draft' : 'Publikasikan Kuis')
                        ->icon(fn (Quiz $record): string => $record->status === 'published' ? 'heroicon-m-eye-slash' : 'heroicon-m-check-circle')
                        ->color(fn (Quiz $record): string => $record->status === 'published' ? 'warning' : 'success')
                        ->action(function (Quiz $record) {
                            $newStatus = $record->status === 'published' ? 'draft' : 'published';
                            $record->update(['status' => $newStatus]);
                            Notification::make()
                                ->title($newStatus === 'published' ? 'Kuis dipublikasikan' : 'Kuis diubah menjadi draft')
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
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
