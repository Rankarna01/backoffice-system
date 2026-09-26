<?php

namespace App\Filament\Resources\MarketNews\Tables;

use App\Domain\Market\Models\MarketNews;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class MarketNewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('cover_image_url')
                    ->label('Thumbnail')
                    ->circular()
                    ->defaultImageUrl('https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=100&auto=format&fit=crop&q=60')
                    ->extraHeaderAttributes(['style' => 'width: 70px;'])
                    ->extraCellAttributes(['style' => 'width: 70px;']),

                TextColumn::make('title')
                    ->label('Judul Berita Finansial')
                    ->searchable(['title', 'summary', 'source', 'slug'])
                    ->sortable()
                    ->weight('semibold')
                    ->wrap()
                    ->grow(true)
                    ->description(fn (MarketNews $record): ?string => $record->summary)
                    ->extraHeaderAttributes(['style' => 'min-width: 320px;'])
                    ->extraCellAttributes(['style' => 'min-width: 320px;']),

                TextColumn::make('impact_level')
                    ->label('Dampak Volatilitas')
                    ->badge()
                    ->color(fn (MarketNews $record): string => $record->impact_color)
                    ->formatStateUsing(fn (MarketNews $record): string => $record->impact_label)
                    ->sortable()
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 140px;'])
                    ->extraCellAttributes(['style' => 'min-width: 140px;']),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (MarketNews $record): string => match ($record->category) {
                        'central_banks' => 'Bank Sentral',
                        'commodities' => 'Komoditas',
                        'forex' => 'Forex',
                        'crypto' => 'Kripto',
                        'indices' => 'Indeks Saham',
                        default => 'Ekonomi Makro',
                    })
                    ->sortable()
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 120px;'])
                    ->extraCellAttributes(['style' => 'min-width: 120px;']),

                TextColumn::make('sentiment')
                    ->label('Sentimen')
                    ->badge()
                    ->color(fn (MarketNews $record): string => $record->sentiment_color)
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bullish' => 'Bullish 🐂',
                        'bearish' => 'Bearish 🐻',
                        default => 'Neutral ⚖️',
                    })
                    ->sortable()
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 100px;'])
                    ->extraCellAttributes(['style' => 'min-width: 100px;']),

                IconColumn::make('is_breaking')
                    ->label('Flash ⚡')
                    ->boolean()
                    ->trueIcon('heroicon-m-bolt')
                    ->falseIcon('heroicon-m-minus')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 70px;'])
                    ->extraCellAttributes(['style' => 'min-width: 70px;']),

                TextColumn::make('source')
                    ->label('Sumber')
                    ->placeholder('Internal')
                    ->badge()
                    ->color('gray')
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
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 100px;'])
                    ->extraCellAttributes(['style' => 'min-width: 100px;']),

                TextColumn::make('published_at')
                    ->label('Waktu Rilis')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                SelectFilter::make('impact_level')
                    ->label('Tingkat Dampak Volatilitas')
                    ->options([
                        'high' => 'High Impact (Volatilitas Ekstrem 🔴)',
                        'medium' => 'Medium Impact 🟡',
                        'low' => 'Low Impact 🟢',
                    ]),

                SelectFilter::make('category')
                    ->label('Kategori Berita')
                    ->options([
                        'central_banks' => 'Kebijakan Bank Sentral',
                        'commodities' => 'Komoditas (Emas/Minyak)',
                        'forex' => 'Forex Currencies',
                        'crypto' => 'Kripto Derivatif',
                        'indices' => 'Indeks Saham Global',
                        'macro_economy' => 'Ekonomi Makro',
                    ]),

                SelectFilter::make('sentiment')
                    ->label('Sentimen Pasar')
                    ->options([
                        'bullish' => 'Bullish 🐂',
                        'bearish' => 'Bearish 🐻',
                        'neutral' => 'Neutral ⚖️',
                    ]),

                TernaryFilter::make('is_breaking')
                    ->label('Breaking News / Flash Alert')
                    ->trueLabel('Hanya Breaking News ⚡')
                    ->falseLabel('Berita Reguler'),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'published' => 'Published',
                        'draft' => 'Draft',
                        'archived' => 'Archived',
                    ]),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('preview')
                        ->label('Pratinjau Berita')
                        ->icon('heroicon-m-eye')
                        ->color('info')
                        ->modalHeading(fn (MarketNews $record): string => "Pratinjau: {$record->title}")
                        ->modalContent(fn (MarketNews $record) => new HtmlString('
                            <div class="space-y-3">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-danger-500/20 text-danger-400">' . e($record->impact_label) . '</span>
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-info-500/20 text-info-400">' . e($record->category_label) . '</span>
                                    <span class="text-xs text-gray-400">Sumber: ' . e($record->source ?: 'Tim Analis') . ' • ' . $record->published_at?->format('d M Y, H:i') . '</span>
                                </div>
                                <p class="text-sm italic text-gray-200 bg-gray-900/70 p-3 rounded-lg border-l-4 border-primary-500">' . e($record->summary ?: 'Tidak ada ringkasan.') . '</p>
                                <div class="text-xs text-gray-300 max-h-[300px] overflow-y-auto prose prose-invert prose-sm">' . $record->content . '</div>
                            </div>
                        ')),

                    Action::make('toggle_breaking')
                        ->label(fn (MarketNews $record): string => $record->is_breaking ? 'Nonaktifkan Flash Alert' : 'Jadikan Breaking News ⚡')
                        ->icon(fn (MarketNews $record): string => $record->is_breaking ? 'heroicon-m-bolt-slash' : 'heroicon-m-bolt')
                        ->color('warning')
                        ->action(function (MarketNews $record) {
                            $record->update(['is_breaking' => ! $record->is_breaking]);
                            Notification::make()
                                ->title($record->is_breaking ? 'Ditandai sebagai Breaking News!' : 'Breaking News dinonaktifkan')
                                ->success()
                                ->send();
                        }),

                    Action::make('toggle_publish')
                        ->label(fn (MarketNews $record): string => $record->status === 'published' ? 'Ubah ke Draft' : 'Tayangkan Berita')
                        ->icon(fn (MarketNews $record): string => $record->status === 'published' ? 'heroicon-m-eye-slash' : 'heroicon-m-check-circle')
                        ->color(fn (MarketNews $record): string => $record->status === 'published' ? 'warning' : 'success')
                        ->action(function (MarketNews $record) {
                            $newStatus = $record->status === 'published' ? 'draft' : 'published';
                            $record->update(['status' => $newStatus]);
                            Notification::make()
                                ->title($newStatus === 'published' ? 'Berita ditayangkan' : 'Berita diubah ke draft')
                                ->success()
                                ->send();
                        }),

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
