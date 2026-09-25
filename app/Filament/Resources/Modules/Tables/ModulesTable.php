<?php

namespace App\Filament\Resources\Modules\Tables;

use App\Domain\Learning\Models\Module;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ModulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
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
                    ->grow(true)
                    ->wrap()
                    ->extraHeaderAttributes(['style' => 'min-width: 320px; max-width: 480px;'])
                    ->extraCellAttributes(['style' => 'min-width: 320px; max-width: 480px;']),

                TextColumn::make('course.title')
                    ->label('Kursus')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->wrap()
                    ->extraHeaderAttributes(['style' => 'min-width: 240px; max-width: 360px;'])
                    ->extraCellAttributes(['style' => 'min-width: 240px; max-width: 360px;']),

                TextColumn::make('document_file')
                    ->label('Dokumen / Lampiran')
                    ->badge()
                    ->color(fn (Module $record): string => match ($record->document_extension) {
                        'pdf' => 'danger',
                        'ppt', 'pptx' => 'warning',
                        'xls', 'xlsx', 'csv' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (Module $record): ?string => match ($record->document_extension) {
                        'pdf' => 'heroicon-m-document-text',
                        'ppt', 'pptx' => 'heroicon-m-presentation-chart-bar',
                        'xls', 'xlsx', 'csv' => 'heroicon-m-table-cells',
                        default => null,
                    })
                    ->formatStateUsing(fn ($state, Module $record): string => $record->document_type_label ? "{$record->document_type_label}" : 'Tanpa File')
                    ->url(fn (Module $record): ?string => $record->document_url, shouldOpenInNewTab: true)
                    ->tooltip(fn (Module $record): ?string => $record->document_file ? 'Buka dokumen ' . strtoupper($record->document_extension) . ' (bisa di-embed di frontend)' : null)
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 140px;'])
                    ->extraCellAttributes(['style' => 'min-width: 140px;']),

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

                SelectFilter::make('has_document')
                    ->label('Filter Dokumen')
                    ->options([
                        'pdf' => 'File PDF (Bisa di-embed)',
                        'presentation' => 'File PowerPoint (PPT/PPTX)',
                        'spreadsheet' => 'File Excel (XLS/XLSX/CSV)',
                        'none' => 'Tanpa Dokumen',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'pdf') {
                            $query->where('document_file', 'like', '%.pdf');
                        } elseif ($data['value'] === 'presentation') {
                            $query->where(function ($q) {
                                $q->where('document_file', 'like', '%.ppt')
                                    ->orWhere('document_file', 'like', '%.pptx');
                            });
                        } elseif ($data['value'] === 'spreadsheet') {
                            $query->where(function ($q) {
                                $q->where('document_file', 'like', '%.xls')
                                    ->orWhere('document_file', 'like', '%.xlsx')
                                    ->orWhere('document_file', 'like', '%.csv');
                            });
                        } elseif ($data['value'] === 'none') {
                            $query->whereNull('document_file');
                        }
                    }),

                SelectFilter::make('is_published')
                    ->label('Status Publikasi')
                    ->options([
                        '1' => 'Published',
                        '0' => 'Draft',
                    ]),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),

                    Action::make('toggle_publish')
                        ->label(fn (Module $record): string => $record->is_published ? 'Ubah ke Draft' : 'Publikasikan Bab')
                        ->icon(fn (Module $record): string => $record->is_published ? 'heroicon-m-eye-slash' : 'heroicon-m-check-circle')
                        ->color(fn (Module $record): string => $record->is_published ? 'warning' : 'success')
                        ->action(function (Module $record) {
                            $record->update(['is_published' => ! $record->is_published]);
                            Notification::make()
                                ->title($record->is_published ? 'Modul dipublikasikan' : 'Modul diubah menjadi draft')
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
