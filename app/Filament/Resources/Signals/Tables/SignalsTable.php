<?php

namespace App\Filament\Resources\Signals\Tables;

use App\Domain\Market\Models\Signal;
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
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class SignalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('pair')
                    ->label('Pair & Setup')
                    ->searchable(['pair', 'title', 'analysis_notes'])
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->grow(true)
                    ->formatStateUsing(fn (string $state, Signal $record): string => "{$state} ({$record->timeframe})")
                    ->description(fn (Signal $record): string => $record->title)
                    ->extraHeaderAttributes(['style' => 'min-width: 280px;'])
                    ->extraCellAttributes(['style' => 'min-width: 280px;']),

                TextColumn::make('market_type')
                    ->label('Pasar')
                    ->badge()
                    ->color(fn (Signal $record): string => $record->market_type_color)
                    ->formatStateUsing(fn (Signal $record): string => match ($record->market_type) {
                        'forex' => 'Forex',
                        'commodities' => 'Komoditas',
                        'crypto' => 'Kripto',
                        'indices' => 'Indeks',
                        default => ucfirst($record->market_type),
                    })
                    ->sortable()
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 110px;'])
                    ->extraCellAttributes(['style' => 'min-width: 110px;']),

                TextColumn::make('action')
                    ->label('Posisi')
                    ->badge()
                    ->color(fn (Signal $record): string => $record->action_color)
                    ->icon(fn (Signal $record): string => str_starts_with($record->action, 'BUY') ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                    ->sortable()
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 110px;'])
                    ->extraCellAttributes(['style' => 'min-width: 110px;']),

                TextColumn::make('entry_price')
                    ->label('Entry')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 4, '.', ''))
                    ->alignRight()
                    ->extraHeaderAttributes(['style' => 'min-width: 100px;'])
                    ->extraCellAttributes(['style' => 'min-width: 100px;']),

                TextColumn::make('stop_loss')
                    ->label('Stop Loss')
                    ->color('danger')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 4, '.', ''))
                    ->alignRight()
                    ->extraHeaderAttributes(['style' => 'min-width: 100px;'])
                    ->extraCellAttributes(['style' => 'min-width: 100px;']),

                TextColumn::make('take_profit_1')
                    ->label('Take Profit 1')
                    ->color('success')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 4, '.', ''))
                    ->alignRight()
                    ->extraHeaderAttributes(['style' => 'min-width: 110px;'])
                    ->extraCellAttributes(['style' => 'min-width: 110px;']),

                TextColumn::make('risk_reward_ratio')
                    ->label('R:R')
                    ->badge()
                    ->color('info')
                    ->placeholder('-')
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 80px;'])
                    ->extraCellAttributes(['style' => 'min-width: 80px;']),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (Signal $record): string => $record->status_color)
                    ->formatStateUsing(fn (Signal $record): string => $record->status_label)
                    ->sortable()
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 140px;'])
                    ->extraCellAttributes(['style' => 'min-width: 140px;']),

                TextColumn::make('result_pips')
                    ->label('Hasil Pips')
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        (float) $state > 0 => 'success',
                        (float) $state < 0 => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state): string => filled($state) ? (((float) $state > 0 ? '+' : '') . (float) $state . ' pips') : '-')
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 110px;'])
                    ->extraCellAttributes(['style' => 'min-width: 110px;']),

                TextColumn::make('is_premium')
                    ->label('Akses')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'warning' : 'gray')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'VIP' : 'FREE')
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 80px;'])
                    ->extraCellAttributes(['style' => 'min-width: 80px;']),

                TextColumn::make('created_at')
                    ->label('Terbit')
                    ->dateTime('d M, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('market_type')
                    ->label('Kategori Pasar')
                    ->options([
                        'forex' => 'Forex Currencies',
                        'commodities' => 'Komoditas (Emas/Minyak)',
                        'crypto' => 'Kripto Derivatif',
                        'indices' => 'Indeks Saham Global',
                    ]),

                SelectFilter::make('action')
                    ->label('Arah Posisi')
                    ->options([
                        'BUY' => 'BUY',
                        'SELL' => 'SELL',
                        'BUY_LIMIT' => 'BUY LIMIT',
                        'SELL_LIMIT' => 'SELL LIMIT',
                    ]),

                SelectFilter::make('status')
                    ->label('Status Sinyal')
                    ->options([
                        'active' => 'Active Running',
                        'pending' => 'Pending Order',
                        'hit_tp1' => 'Hit TP 1',
                        'hit_tp2' => 'Hit TP 2',
                        'hit_tp3' => 'Hit TP 3',
                        'hit_sl' => 'Hit SL',
                        'closed' => 'Closed',
                    ]),

                TernaryFilter::make('is_premium')
                    ->label('Tipe Akses')
                    ->trueLabel('Hanya VIP / Premium')
                    ->falseLabel('Hanya Free Sinyal'),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('mark_hit_tp')
                        ->label('Update: Hit TP')
                        ->icon('bx-target-lock')
                        ->color('success')
                        ->form([
                            TextInput::make('pips')
                                ->label('Perolehan Pips Keuntungan')
                                ->numeric()
                                ->default(60)
                                ->required(),
                        ])
                        ->action(function (Signal $record, array $data) {
                            $record->update([
                                'status' => 'hit_tp1',
                                'result_pips' => (float) $data['pips'],
                                'closed_at' => now(),
                            ]);
                            Notification::make()
                                ->title('Sinyal berhasil diperbarui: Hit TP1!')
                                ->success()
                                ->send();
                        }),

                    Action::make('mark_hit_sl')
                        ->label('Update: Hit SL')
                        ->icon('bx-x-circle')
                        ->color('danger')
                        ->form([
                            TextInput::make('loss_pips')
                                ->label('Pips Kerugian (Kena SL)')
                                ->numeric()
                                ->default(-25)
                                ->required(),
                        ])
                        ->action(function (Signal $record, array $data) {
                            $loss = (float) $data['loss_pips'];
                            $record->update([
                                'status' => 'hit_sl',
                                'result_pips' => $loss > 0 ? -$loss : $loss,
                                'closed_at' => now(),
                            ]);
                            Notification::make()
                                ->title('Sinyal diperbarui: Hit Stop Loss')
                                ->danger()
                                ->send();
                        }),

                    Action::make('view_chart')
                        ->label('Lihat Screenshot Chart')
                        ->icon('heroicon-m-photo')
                        ->color('info')
                        ->visible(fn (Signal $record): bool => filled($record->chart_image_url))
                        ->modalHeading(fn (Signal $record): string => "Analisa Chart {$record->pair} ({$record->timeframe})")
                        ->modalContent(fn (Signal $record) => new HtmlString('
                            <div class="space-y-4">
                                <div class="max-h-[500px] overflow-hidden rounded-lg flex items-center justify-center bg-gray-950">
                                    <img src="' . e($record->chart_image_url) . '" alt="Chart Setup" class="max-h-[450px] object-contain" />
                                </div>
                                <div class="p-3 bg-gray-900/50 rounded text-sm text-gray-300">
                                    <p class="font-semibold text-white mb-1">Catatan Analisa:</p>
                                    <p>' . nl2br(e($record->analysis_notes ?: 'Tidak ada catatan tambahan.')) . '</p>
                                </div>
                            </div>
                        ')),

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
