<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Domain\Billing\Enums\OrderStatus;
use App\Domain\Billing\Enums\PaymentGateway;
use App\Domain\Billing\Enums\PaymentStatus;
use App\Domain\Billing\Models\Payment;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('order.number')
                    ->label('Nomor Pesanan')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (Payment $record): ?string => $record->gateway_ref ? "Ref: {$record->gateway_ref}" : null),

                TextColumn::make('order.user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Payment $record): ?string => $record->order?->user?->email),

                TextColumn::make('gateway')
                    ->label('Gateway')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => $state instanceof PaymentGateway ? $state->getLabel() : (string) $state)
                    ->icon(fn ($state) => $state instanceof PaymentGateway ? $state->getIcon() : null),

                TextColumn::make('method')
                    ->label('Metode Bayar')
                    ->badge()
                    ->color('gray')
                    ->default('Transfer Bank'),

                TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('fee')
                    ->label('Fee')
                    ->money('IDR', locale: 'id')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state instanceof PaymentStatus ? $state->getColor() : 'gray')
                    ->formatStateUsing(fn ($state) => $state instanceof PaymentStatus ? $state->getLabel() : (string) $state)
                    ->icon(fn ($state) => $state instanceof PaymentStatus ? $state->getIcon() : null)
                    ->sortable(),

                TextColumn::make('paid_at')
                    ->label('Tanggal Lunas')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Belum Lunas')
                    ->color('gray')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pembayaran')
                    ->options(PaymentStatus::options()),

                SelectFilter::make('gateway')
                    ->label('Payment Gateway')
                    ->options(PaymentGateway::options()),
            ])
            ->recordActions([
                Action::make('confirm_payment')
                    ->label('Konfirmasi Bayar')
                    ->icon('bx-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran Manual')
                    ->modalDescription('Apakah Anda yakin ingin memverifikasi pelunasan transaksi ini? Status pembayaran dan pesanan terkait akan otomatis diset LUNAS.')
                    ->visible(fn (Payment $record): bool => $record->status === PaymentStatus::Pending)
                    ->action(function (Payment $record) {
                        $now = now();
                        $record->update([
                            'status' => PaymentStatus::Paid,
                            'paid_at' => $now,
                        ]);

                        if ($record->order && $record->order->status !== OrderStatus::Paid) {
                            $record->order->update([
                                'status' => OrderStatus::Paid,
                                'paid_at' => $now,
                            ]);
                        }

                        Notification::make()
                            ->title('Pembayaran Berhasil Dikonfirmasi')
                            ->body('Transaksi dan pesanan terkait telah diset Lunas.')
                            ->success()
                            ->send();
                    }),

                EditAction::make()->icon('bx-edit'),
                DeleteAction::make()->icon('bx-trash'),
            ]);
    }
}
