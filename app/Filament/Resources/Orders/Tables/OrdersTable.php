<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Domain\Billing\Enums\OrderStatus;
use App\Domain\Billing\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('number')
                    ->label('Nomor Order')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->copyable()
                    ->copyMessage('Nomor order disalin!'),

                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (Order $record): ?string => $record->user?->email),

                TextColumn::make('items_summary')
                    ->label('Item Pesanan')
                    ->state(function (Order $record): string {
                        $items = $record->items->pluck('name');
                        return $items->isNotEmpty() ? $items->join(', ') : 'Paket Langganan';
                    })
                    ->limit(35)
                    ->color('gray'),

                TextColumn::make('total')
                    ->label('Total Tagihan')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state instanceof OrderStatus ? $state->getColor() : 'gray')
                    ->formatStateUsing(fn ($state) => $state instanceof OrderStatus ? $state->getLabel() : (string) $state)
                    ->icon(fn ($state) => $state instanceof OrderStatus ? $state->getIcon() : null)
                    ->sortable(),

                TextColumn::make('coupon.code')
                    ->label('Kupon')
                    ->badge()
                    ->color('primary')
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Tanggal Order')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                TextColumn::make('paid_at')
                    ->label('Waktu Bayar')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Belum Lunas')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pesanan')
                    ->options(OrderStatus::options()),

                TernaryFilter::make('has_coupon')
                    ->label('Memakai Kupon Diskon')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('coupon_id'),
                        false: fn ($query) => $query->whereNull('coupon_id'),
                    ),

                TernaryFilter::make('has_affiliate')
                    ->label('Dari Kode Referral / Affiliate')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('affiliate_id')->orWhereNotNull('referral_code'),
                        false: fn ($query) => $query->whereNull('affiliate_id')->whereNull('referral_code'),
                    ),
            ])
            ->recordActions([
                Action::make('mark_as_paid')
                    ->label('Tandai Lunas')
                    ->icon('bx-check-double')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pelunasan Pesanan Manual')
                    ->modalDescription('Tandai pesanan ini lunas secara manual? Akses materi/langganan akan otomatis diaktifkan untuk user.')
                    ->visible(fn (Order $record): bool => $record->status === OrderStatus::Pending)
                    ->action(function (Order $record) {
                        $record->update([
                            'status' => OrderStatus::Paid,
                            'paid_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Pesanan Ditandai Lunas')
                            ->body("Order #{$record->number} telah berstatus Lunas.")
                            ->success()
                            ->send();
                    }),

                Action::make('cancel_order')
                    ->label('Batalkan')
                    ->icon('bx-x')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record): bool => $record->status === OrderStatus::Pending)
                    ->action(function (Order $record) {
                        $record->update(['status' => OrderStatus::Expired]);

                        Notification::make()
                            ->title('Pesanan Dibatalkan / Kedaluwarsa')
                            ->warning()
                            ->send();
                    }),

                EditAction::make()->icon('bx-edit'),
                DeleteAction::make()->icon('bx-trash'),
            ]);
    }
}
