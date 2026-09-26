<?php

namespace App\Filament\Resources\MarketOutlooks\Tables;

use App\Domain\Market\Models\MarketOutlook;
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
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class MarketOutlooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('cover_image_url')
                    ->label('Cover')
                    ->circular()
                    ->defaultImageUrl('https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=100&auto=format&fit=crop&q=60')
                    ->extraHeaderAttributes(['style' => 'width: 70px;'])
                    ->extraCellAttributes(['style' => 'width: 70px;']),

                TextColumn::make('title')
                    ->label('Judul Ulasan Makro')
                    ->searchable(['title', 'summary', 'slug'])
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->grow(true)
                    ->description(fn (MarketOutlook $record): ?string => $record->summary)
                    ->extraHeaderAttributes(['style' => 'min-width: 320px;'])
                    ->extraCellAttributes(['style' => 'min-width: 320px;']),

                TextColumn::make('sentiment')
                    ->label('Sentimen')
                    ->badge()
                    ->color(fn (MarketOutlook $record): string => $record->sentiment_color)
                    ->formatStateUsing(fn (MarketOutlook $record): string => $record->sentiment_label)
                    ->sortable()
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 140px;'])
                    ->extraCellAttributes(['style' => 'min-width: 140px;']),

                TextColumn::make('market_category')
                    ->label('Pasar')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (MarketOutlook $record): string => match ($record->market_category) {
                        'commodities' => 'Komoditas',
                        'forex' => 'Forex',
                        'crypto' => 'Kripto',
                        'indices' => 'Indeks',
                        default => 'Multi-Asset',
                    })
                    ->sortable()
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 120px;'])
                    ->extraCellAttributes(['style' => 'min-width: 120px;']),

                TextColumn::make('time_horizon')
                    ->label('Horizon')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'weekly' => 'Mingguan',
                        'monthly' => 'Bulanan',
                        'quarterly' => 'Kuartalan',
                        default => 'Special',
                    })
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 100px;'])
                    ->extraCellAttributes(['style' => 'min-width: 100px;']),

                TextColumn::make('featured_pairs')
                    ->label('Pair Terkait')
                    ->badge()
                    ->color('primary')
                    ->separator(',')
                    ->limitList(2)
                    ->placeholder('-')
                    ->extraHeaderAttributes(['style' => 'min-width: 140px;'])
                    ->extraCellAttributes(['style' => 'min-width: 140px;']),

                TextColumn::make('is_premium')
                    ->label('Akses')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'warning' : 'gray')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'VIP' : 'FREE')
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 80px;'])
                    ->extraCellAttributes(['style' => 'min-width: 80px;']),

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
                    ->label('Rilis')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                SelectFilter::make('market_category')
                    ->label('Kategori Pasar')
                    ->options([
                        'commodities' => 'Komoditas (Emas/Minyak)',
                        'forex' => 'Forex Currencies',
                        'crypto' => 'Kripto Derivatif',
                        'indices' => 'Indeks Saham',
                        'multi_asset' => 'Multi-Asset Macro',
                    ]),

                SelectFilter::make('sentiment')
                    ->label('Sentimen Pasar')
                    ->options([
                        'bullish' => 'Bullish (Naik)',
                        'bearish' => 'Bearish (Turun)',
                        'neutral' => 'Neutral (Sideways)',
                        'volatile' => 'Volatile (Fluktuatif)',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'published' => 'Published',
                        'draft' => 'Draft',
                        'archived' => 'Archived',
                    ]),

                TernaryFilter::make('is_premium')
                    ->label('Akses')
                    ->trueLabel('Hanya VIP / Premium')
                    ->falseLabel('Hanya Free Artikel'),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('preview')
                        ->label('Ringkasan Cepat')
                        ->icon('bx-show')
                        ->color('info')
                        ->modalHeading(fn (MarketOutlook $record): string => "Ulasan: {$record->title}")
                        ->modalContent(function (MarketOutlook $record) {
                            $takeawaysHtml = '';
                            if (! empty($record->key_takeaways)) {
                                $takeawaysHtml .= '<div class="space-y-1 my-3"><p class="font-semibold text-sm text-gray-200">Poin Kunci (Key Takeaways):</p><ul class="list-disc pl-5 text-xs text-gray-300 space-y-1">';
                                foreach ($record->key_takeaways as $item) {
                                    $takeawaysHtml .= '<li>' . e($item) . '</li>';
                                }
                                $takeawaysHtml .= '</ul></div>';
                            }

                            $levelsHtml = '';
                            if (! empty($record->support_resistance_levels)) {
                                $levelsHtml .= '<div class="my-3"><p class="font-semibold text-sm text-gray-200 mb-2">Level Kunci Harga:</p><div class="overflow-x-auto"><table class="w-full text-xs text-left border border-gray-800"><thead class="bg-gray-900 text-gray-400"><tr><th class="p-2">Pair</th><th class="p-2">Support</th><th class="p-2">Resistance</th><th class="p-2">Bias</th></tr></thead><tbody>';
                                foreach ($record->support_resistance_levels as $lvl) {
                                    $levelsHtml .= '<tr class="border-t border-gray-800"><td class="p-2 font-bold">' . e($lvl['pair'] ?? '-') . '</td><td class="p-2 text-danger-400">' . e($lvl['support'] ?? '-') . '</td><td class="p-2 text-success-400">' . e($lvl['resistance'] ?? '-') . '</td><td class="p-2">' . e($lvl['bias'] ?? '-') . '</td></tr>';
                                }
                                $levelsHtml .= '</tbody></table></div></div>';
                            }

                            return new HtmlString('
                                <div class="space-y-3">
                                    <p class="text-sm italic text-gray-300 bg-gray-900/60 p-3 rounded-lg border-l-4 border-primary-500">' . e($record->summary ?: 'Tidak ada ringkasan.') . '</p>
                                    ' . $takeawaysHtml . '
                                    ' . $levelsHtml . '
                                </div>
                            ');
                        }),

                    Action::make('toggle_publish')
                        ->label(fn (MarketOutlook $record): string => $record->status === 'published' ? 'Ubah ke Draft' : 'Tayangkan Ulasan')
                        ->icon(fn (MarketOutlook $record): string => $record->status === 'published' ? 'heroicon-m-eye-slash' : 'heroicon-m-check-circle')
                        ->color(fn (MarketOutlook $record): string => $record->status === 'published' ? 'warning' : 'success')
                        ->action(function (MarketOutlook $record) {
                            $newStatus = $record->status === 'published' ? 'draft' : 'published';
                            $record->update(['status' => $newStatus]);
                            Notification::make()
                                ->title($newStatus === 'published' ? 'Market Outlook ditayangkan' : 'Diubah menjadi draf')
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
