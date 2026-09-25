<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Domain\Learning\Models\Module;
use App\Filament\Resources\Modules\ModuleResource;
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

class ModulesRelationManager extends RelationManager
{
    protected static string $relationship = 'modules';

    protected static ?string $title = 'Silabus Bab & Modul Kursus';

    protected static ?string $modelLabel = 'Modul / Bab';

    protected static ?string $pluralModelLabel = 'Modul-Modul Kursus';

    public function form(Schema $schema): Schema
    {
        return ModuleResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Bab #')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('primary')
                    ->extraHeaderAttributes(['style' => 'min-width: 80px;'])
                    ->extraCellAttributes(['style' => 'min-width: 80px;']),

                TextColumn::make('title')
                    ->label('Judul Modul / Bab')
                    ->description(fn (Module $record): ?string => $record->description)
                    ->searchable(['title', 'description'])
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->grow(true)
                    ->url(fn (Module $record): string => ModuleResource::getUrl('edit', ['record' => $record]))
                    ->extraHeaderAttributes(['style' => 'min-width: 320px;'])
                    ->extraCellAttributes(['style' => 'min-width: 320px;']),

                TextColumn::make('is_published')
                    ->label('Status')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Published' : 'Draft')
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 110px;'])
                    ->extraCellAttributes(['style' => 'min-width: 110px;']),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_published')
                    ->label('Status')
                    ->options([
                        '1' => 'Published',
                        '0' => 'Draft',
                    ]),

                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Bab / Modul Baru')
                    ->url(fn (): string => ModuleResource::getUrl('create', [
                        'course_id' => $this->getOwnerRecord()->getKey(),
                    ]))
                    ->modal(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->url(fn (Module $record): string => ModuleResource::getUrl('edit', ['record' => $record]))
                        ->modal(false),

                    Action::make('toggle_publish')
                        ->label(fn (Module $record): string => $record->is_published ? 'Ubah ke Draft' : 'Publikasikan Bab')
                        ->icon(fn (Module $record): string => $record->is_published ? 'heroicon-m-eye-slash' : 'heroicon-m-check-circle')
                        ->color(fn (Module $record): string => $record->is_published ? 'warning' : 'success')
                        ->action(function (Module $record) {
                            $record->update(['is_published' => ! $record->is_published]);
                            Notification::make()
                                ->title($record->is_published ? 'Bab dipublikasikan' : 'Bab diubah menjadi draft')
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
