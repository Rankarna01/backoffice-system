<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Domain\Billing\Enums\PaymentGateway;
use App\Domain\Billing\Enums\PaymentStatus;
use App\Domain\Billing\Models\Order;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Transaksi & Gateway')
                ->description('Rincian gateway pembayaran, nomor referensi, dan saluran metode bayar.')
                ->schema([
                    Select::make('order_id')
                        ->label('Nomor Pesanan Terkait')
                        ->options(fn () => Order::pluck('number', 'id'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpan(1),

                    Select::make('gateway')
                        ->label('Payment Gateway')
                        ->options(PaymentGateway::options())
                        ->default(PaymentGateway::Midtrans->value)
                        ->required()
                        ->columnSpan(1),

                    TextInput::make('gateway_ref')
                        ->label('Referensi Transaksi Gateway')
                        ->placeholder('ID Transaksi (Midtrans/Xendit/Bank Ref)')
                        ->maxLength(255)
                        ->columnSpan(1),

                    TextInput::make('method')
                        ->label('Metode Pembayaran')
                        ->placeholder('Contoh: QRIS, BCA Virtual Account, Mandiri, GoPay')
                        ->maxLength(100)
                        ->columnSpan(1),

                    Select::make('status')
                        ->label('Status Pembayaran')
                        ->options(PaymentStatus::options())
                        ->default(PaymentStatus::Pending->value)
                        ->required()
                        ->columnSpan(1),

                    TextInput::make('currency')
                        ->label('Mata Uang')
                        ->default('IDR')
                        ->disabled()
                        ->columnSpan(1),
                ])->columns(2),

            Section::make('Nominal & Waktu Pembayaran')
                ->description('Jumlah dana yang dibayar, estimasi biaya gateway, dan waktu transaksi.')
                ->schema([
                    TextInput::make('amount')
                        ->label('Jumlah Dibayar (Amount)')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),

                    TextInput::make('fee')
                        ->label('Biaya Admin Gateway (Fee)')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0),

                    TextInput::make('payment_url')
                        ->label('URL Halaman Pembayaran (Snap URL)')
                        ->url()
                        ->placeholder('https://app.midtrans.com/snap/v2/...')
                        ->columnSpanFull(),

                    DateTimePicker::make('paid_at')
                        ->label('Waktu Pembayaran Selesai (Paid At)')
                        ->native(false)
                        ->seconds(false),

                    DateTimePicker::make('expires_at')
                        ->label('Waktu Kedaluwarsa Sesi Bayar')
                        ->native(false)
                        ->seconds(false),
                ])->columns(2),
        ]);
    }
}
