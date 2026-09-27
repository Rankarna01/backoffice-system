<?php

namespace App\Filament\Resources\Coupons\Schemas;

use App\Domain\Billing\Enums\CouponType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Kupon & Nilai Potongan')
                ->description('Tentukan kode kupon unik dan persentase atau nominal diskon.')
                ->schema([
                    TextInput::make('code')
                        ->label('Kode Kupon Promo')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50)
                        ->placeholder('Contoh: TRADINGPRO50')
                        ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                        ->columnSpan(1),

                    TextInput::make('name')
                        ->label('Nama Kampanye / Promo')
                        ->placeholder('Contoh: Promo Gajian Akhir Bulan')
                        ->maxLength(255)
                        ->columnSpan(1),

                    Select::make('type')
                        ->label('Tipe Potongan')
                        ->options(CouponType::options())
                        ->default(CouponType::Percent->value)
                        ->required()
                        ->live()
                        ->columnSpan(1),

                    TextInput::make('value')
                        ->label('Besaran Diskon')
                        ->numeric()
                        ->required()
                        ->helperText('Persen (misal 20 untuk 20%) atau Rupiah nominal.')
                        ->columnSpan(1),

                    TextInput::make('max_discount')
                        ->label('Maksimal Potongan (Rp)')
                        ->numeric()
                        ->prefix('Rp')
                        ->helperText('Batas diskon tertinggi bila menggunakan tipe persentase.')
                        ->columnSpan(1),

                    TextInput::make('min_order')
                        ->label('Minimal Belanja (Rp)')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->helperText('Minimal subtotal pesanan sebelum kupon dapat digunakan.')
                        ->columnSpan(1),

                    Select::make('applies_to')
                        ->label('Berlaku Untuk')
                        ->options([
                            'all' => 'Semua Produk (Kursus & Paket)',
                            'courses' => 'Khusus Kursus Satuan',
                            'plans' => 'Khusus Paket Langganan',
                        ])
                        ->default('all')
                        ->required()
                        ->columnSpan(1),

                    Toggle::make('is_active')
                        ->label('Status Kupon Aktif')
                        ->default(true)
                        ->helperText('Matikan untuk menonaktifkan kode promo ini segera.')
                        ->columnSpan(1),
                ])->columns(2),

            Section::make('Batasan Kuota & Periode Berlaku')
                ->description('Atur limit kuota pemakaian dan masa berlaku kupon.')
                ->schema([
                    DateTimePicker::make('starts_at')
                        ->label('Mulai Berlaku')
                        ->native(false)
                        ->seconds(false)
                        ->default(now()),

                    DateTimePicker::make('ends_at')
                        ->label('Berakhir Pada')
                        ->native(false)
                        ->seconds(false)
                        ->helperText('Kosongkan bila tidak ada batas waktu kedaluwarsa.'),

                    TextInput::make('max_redemptions')
                        ->label('Maksimal Kuota Penggunaan')
                        ->numeric()
                        ->default(0)
                        ->helperText('Isi 0 jika kuota tidak terbatas (unlimited).'),

                    TextInput::make('max_per_user')
                        ->label('Batas Maksimal Per Pengguna')
                        ->numeric()
                        ->default(1)
                        ->helperText('Berapa kali satu akun boleh menggunakan kupon ini.'),

                    TextInput::make('redemptions_count')
                        ->label('Jumlah Sudah Digunakan')
                        ->numeric()
                        ->default(0)
                        ->disabled(),
                ])->columns(2),
        ]);
    }
}
