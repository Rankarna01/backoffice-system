<?php

namespace App\Filament\Resources\Affiliates\Schemas;

use App\Domain\Billing\Enums\AffiliateStatus;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AffiliateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Mitra Afiliasi')
                ->description('Pilih akun pengguna, kode referral unik, dan tingkat komisi.')
                ->schema([
                    Select::make('user_id')
                        ->label('Akun Pengguna')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpan(1),

                    TextInput::make('code')
                        ->label('Kode Referral')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50)
                        ->placeholder('Contoh: ALEX10 atau TRADERPRO')
                        ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                        ->columnSpan(1),

                    TextInput::make('commission_rate')
                        ->label('Persentase Komisi (%)')
                        ->numeric()
                        ->default(20.00)
                        ->suffix('%')
                        ->required()
                        ->columnSpan(1),

                    Select::make('status')
                        ->label('Status Kemitraan')
                        ->options(AffiliateStatus::options())
                        ->default(AffiliateStatus::Approved->value)
                        ->required()
                        ->columnSpan(1),
                ])->columns(2),

            Section::make('Rekening Bank untuk Pencairan (Payout)')
                ->description('Data rekening bank tujuan penarikan komisi mitra.')
                ->schema([
                    TextInput::make('payout_details.bank_name')
                        ->label('Nama Bank / E-Wallet')
                        ->placeholder('Contoh: BCA, Bank Mandiri, BNI, GoPay')
                        ->columnSpan(1),

                    TextInput::make('payout_details.account_number')
                        ->label('Nomor Rekening')
                        ->placeholder('Nomor rekening bank')
                        ->columnSpan(1),

                    TextInput::make('payout_details.account_holder')
                        ->label('Nama Pemilik Rekening')
                        ->placeholder('Sesuai buku tabungan')
                        ->columnSpan(2),
                ])->columns(2),

            Section::make('Ringkasan Keuangan Mitra')
                ->description('Total komisi yang diperoleh dan sudah dicairkan.')
                ->schema([
                    TextInput::make('total_earnings')
                        ->label('Total Komisi Didapatkan')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->disabled(),

                    TextInput::make('total_paid')
                        ->label('Total Komisi Dicairkan')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->disabled(),
                ])->columns(2),
        ]);
    }
}
