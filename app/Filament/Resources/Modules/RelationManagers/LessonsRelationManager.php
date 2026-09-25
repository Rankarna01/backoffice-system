<?php

namespace App\Filament\Resources\Modules\RelationManagers;

use App\Domain\Learning\Models\Lesson;
use App\Filament\Resources\Lessons\LessonResource;
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

class LessonsRelationManager extends RelationManager
{
    protected static string $relationship = 'lessons';

    protected static ?string $title = 'Daftar Materi Pelajaran (Lessons)';

    protected static ?string $modelLabel = 'Pelajaran / Lesson';

    protected static ?string $pluralModelLabel = 'Materi Pelajaran';

    public function form(Schema $schema): Schema
    {
        return LessonResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('primary')
                    ->extraHeaderAttributes(['style' => 'min-width: 60px;'])
                    ->extraCellAttributes(['style' => 'min-width: 60px;']),

                TextColumn::make('title')
                    ->label('Judul Pelajaran')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->grow(true)
                    ->url(fn (Lesson $record): string => LessonResource::getUrl('edit', ['record' => $record]))
                    ->extraHeaderAttributes(['style' => 'min-width: 280px;'])
                    ->extraCellAttributes(['style' => 'min-width: 280px;']),

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
                        'file' => 'File',
                        default => ucfirst($state),
                    })
                    ->alignCenter(),

                TextColumn::make('duration_formatted')
                    ->label('Durasi')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                TextColumn::make('is_preview')
                    ->label('Preview')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Free' : 'Terkunci')
                    ->alignCenter(),

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
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'video' => 'Video',
                        'article' => 'Artikel',
                        'file' => 'File',
                    ]),

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
                    ->label('Tambah Pelajaran Baru')
                    ->url(fn (): string => LessonResource::getUrl('create', [
                        'module_id' => $this->getOwnerRecord()->getKey(),
                        'course_id' => $this->getOwnerRecord()->course_id,
                    ]))
                    ->modal(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->url(fn (Lesson $record): string => LessonResource::getUrl('edit', ['record' => $record]))
                        ->modal(false),

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
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
