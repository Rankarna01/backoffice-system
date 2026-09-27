<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Domain\Billing\Enums\OrderStatus;
use App\Domain\Billing\Models\Coupon;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identitas & Status Pesanan')
                ->description('Rincian nomor pesanan, pembeli, dan status pembayaran.')
                ->schema([
                    TextInput::make('number')
                        ->label('Nomor Pesanan (Order #)')
                        ->disabled()
                        ->placeholder('Otomatis dibuat sistem (e.g. ORD-20260927-XXXX)')
                        ->columnSpan(1),

                    Select::make('user_id')
                        ->label('Pembeli / Customer')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpan(1),

                    Select::make('status')
                        ->label('Status Pesanan')
                        ->options(OrderStatus::options())
                        ->default(OrderStatus::Pending->value)
                        ->required()
                        ->columnSpan(1),

                    Select::make('coupon_id')
                        ->label('Kupon Promosi Digunakan')
                        ->options(fn () => Coupon::pluck('code', 'id'))
                        ->searchable()
                        ->preload()
                        ->placeholder('Tanpa Kupon')
                        ->columnSpan(1),

                    TextInput::make('referral_code')
                        ->label('Kode Referral / Affiliate')
                        ->placeholder('Kode affiliate (jika ada)')
                        ->maxLength(50)
                        ->columnSpan(1),

                    TextInput::make('currency')
                        ->label('Mata Uang')
                        ->default('IDR')
                        ->disabled()
                        ->columnSpan(1),
                ])->columns(2),

            Section::make('Rincian Biaya & Transaksi')
                ->description('Kalkulasi subtotal, diskon kupon, pajak, dan total tagihan.')
                ->schema([
                    TextInput::make('subtotal')
                        ->label('Subtotal Produk')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->required(),

                    TextInput::make('discount_total')
                        ->label('Total Potongan Diskon')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0),

                    TextInput::make('tax_total')
                        ->label('Pajak (PPN)')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0),

                    TextInput::make('total')
                        ->label('Total Tagihan Akhir')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->required(),

                    DateTimePicker::make('paid_at')
                        ->label('Waktu Pelunasan (Paid At)')
                        ->native(false)
                        ->seconds(false),

                    DateTimePicker::make('expires_at')
                        ->label('Batas Waktu Pembayaran')
                        ->native(false)
                        ->seconds(false)
                        ->default(now()->addDay()),
                ])->columns(2),
        ]);
    }
}
