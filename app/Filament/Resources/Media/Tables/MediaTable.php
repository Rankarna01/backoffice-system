<?php

namespace App\Filament\Resources\Media\Tables;

use App\Domain\Media\Models\MediaAsset;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class MediaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('public_url')
                    ->label('Preview')
                    ->circular()
                    ->defaultImageUrl(fn (MediaAsset $record): string => match ($record->type) {
                        'video' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=100&auto=format&fit=crop&q=60',
                        'document' => 'https://images.unsplash.com/photo-1568667256549-094345857637?w=100&auto=format&fit=crop&q=60',
                        default => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=100&auto=format&fit=crop&q=60',
                    })
                    ->extraHeaderAttributes(['style' => 'width: 70px;'])
                    ->extraCellAttributes(['style' => 'width: 70px;']),

                TextColumn::make('name')
                    ->label('Nama Aset & File')
                    ->searchable(['name', 'file_name', 'file_path'])
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->grow(true)
                    ->description(fn (MediaAsset $record): string => "{$record->file_name} • {$record->file_path}")
                    ->extraHeaderAttributes(['style' => 'min-width: 320px;'])
                    ->extraCellAttributes(['style' => 'min-width: 320px;']),

                TextColumn::make('type')
                    ->label('Tipe Media')
                    ->badge()
                    ->color(fn (MediaAsset $record): string => $record->type_badge_color)
                    ->icon(fn (MediaAsset $record): string => $record->type_icon)
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'video' => 'Video Materi',
                        'image' => 'Gambar / Cover',
                        'document' => 'Dokumen / PDF',
                        'audio' => 'Audio Podcast',
                        default => 'Lainnya',
                    })
                    ->sortable()
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 140px;'])
                    ->extraCellAttributes(['style' => 'min-width: 140px;']),

                TextColumn::make('collection')
                    ->label('Folder Direktori')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'courses' => 'courses/thumbnails',
                        'lessons' => 'lessons/videos',
                        'modules' => 'modules/documents',
                        'mentors' => 'mentors/avatars',
                        'marketing' => 'marketing/banners',
                        default => $state,
                    })
                    ->sortable()
                    ->extraHeaderAttributes(['style' => 'min-width: 160px;'])
                    ->extraCellAttributes(['style' => 'min-width: 160px;']),

                TextColumn::make('size_bytes')
                    ->label('Ukuran')
                    ->formatStateUsing(fn (int $state, MediaAsset $record): string => $record->size_formatted)
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 110px;'])
                    ->extraCellAttributes(['style' => 'min-width: 110px;']),

                TextColumn::make('disk')
                    ->label('Storage Engine')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn (): string => 'Cloudflare R2')
                    ->icon('heroicon-m-cloud')
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 140px;'])
                    ->extraCellAttributes(['style' => 'min-width: 140px;']),

                TextColumn::make('created_at')
                    ->label('Diupload')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe Media')
                    ->options([
                        'video' => 'Video Materi Pelajaran',
                        'image' => 'Gambar / Foto Thumbnail',
                        'document' => 'Dokumen PDF / Silabus',
                        'audio' => 'Audio File',
                    ]),

                SelectFilter::make('collection')
                    ->label('Folder Direktori')
                    ->options([
                        'courses' => 'Cover & Thumbnail Kursus',
                        'lessons' => 'Video Materi Pelajaran',
                        'modules' => 'Dokumen Silabus & Modul',
                        'mentors' => 'Foto Mentor & Trainer',
                        'marketing' => 'Banner & Grafis Promosi',
                    ]),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('preview')
                        ->label('Pratinjau / Preview')
                        ->icon('heroicon-m-eye')
                        ->color('info')
                        ->modalHeading(fn (MediaAsset $record): string => "Pratinjau Media: {$record->name}")
                        ->modalContent(function (MediaAsset $record) {
                            if ($record->type === 'video') {
                                return new HtmlString('
                                    <div class="space-y-4">
                                        <div class="aspect-video w-full rounded-lg overflow-hidden bg-black flex items-center justify-center">
                                            <video controls class="w-full h-full max-h-[450px]" src="' . e($record->r2_cdn_url) . '">
                                                Browser Anda tidak mendukung tag video.
                                            </video>
                                        </div>
                                        <p class="text-xs text-gray-500 font-mono">Stream URL: ' . e($record->r2_cdn_url) . '</p>
                                    </div>
                                ');
                            }

                            if ($record->type === 'image') {
                                return new HtmlString('
                                    <div class="space-y-4 text-center">
                                        <div class="max-h-[500px] overflow-hidden rounded-lg flex items-center justify-center bg-gray-950">
                                            <img src="' . e($record->r2_cdn_url) . '" alt="' . e($record->alt_text ?: $record->name) . '" class="max-h-[450px] object-contain" />
                                        </div>
                                        <p class="text-xs text-gray-500 font-mono">' . e($record->r2_cdn_url) . '</p>
                                    </div>
                                ');
                            }

                            return new HtmlString('
                                <div class="p-6 text-center space-y-4">
                                    <div class="inline-flex p-4 rounded-full bg-warning-50 text-warning-600">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <h4 class="text-base font-semibold">' . e($record->name) . '</h4>
                                    <p class="text-sm text-gray-500 font-mono">' . e($record->file_path) . '</p>
                                    <div class="pt-2">
                                        <a href="' . e($record->r2_cdn_url) . '" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-500">
                                            Buka Dokumen di Tab Baru
                                        </a>
                                    </div>
                                </div>
                            ');
                        }),

                    Action::make('copy_r2_url')
                        ->label('Salin URL CDN R2')
                        ->icon('heroicon-m-clipboard-document')
                        ->color('success')
                        ->action(function (MediaAsset $record) {
                            Notification::make()
                                ->title('URL CDN Disalin!')
                                ->body($record->r2_cdn_url)
                                ->success()
                                ->send();
                        }),

                    Action::make('open_external')
                        ->label('Buka Tautan Langsung')
                        ->icon('heroicon-m-arrow-top-right-on-square')
                        ->url(fn (MediaAsset $record): string => $record->r2_cdn_url, shouldOpenInNewTab: true),

                    EditAction::make(),
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
