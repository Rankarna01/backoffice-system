<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use App\Domain\Billing\Enums\SubscriptionStatus;
use App\Domain\Billing\Models\Plan;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Member & Paket')
                ->description('Pilih akun pengguna dan tier keanggotaan langganan.')
                ->schema([
                    Select::make('user_id')
                        ->label('Akun Member / Pengguna')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpan(1),

                    Select::make('plan_id')
                        ->label('Paket Langganan')
                        ->options(fn () => Plan::query()->where('is_active', true)->pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->columnSpan(1),

                    Select::make('status')
                        ->label('Status Akses')
                        ->options(SubscriptionStatus::options())
                        ->default(SubscriptionStatus::Active->value)
                        ->required()
                        ->columnSpan(1),

                    Toggle::make('auto_renew')
                        ->label('Perpanjangan Otomatis (Auto-Renew)')
                        ->default(false)
                        ->helperText('Perpanjang langganan otomatis jika saldo/kartu terhubung.')
                        ->columnSpan(1),
                ])->columns(2),

            Section::make('Masa Berlaku & Catatan')
                ->description('Atur tanggal mulai, tanggal kedaluwarsa, dan masa tenggang.')
                ->schema([
                    DateTimePicker::make('starts_at')
                        ->label('Tanggal Mulai')
                        ->default(now())
                        ->native(false)
                        ->seconds(false)
                        ->required(),

                    DateTimePicker::make('ends_at')
                        ->label('Tanggal Kedaluwarsa')
                        ->native(false)
                        ->seconds(false)
                        ->helperText('Kosongkan jika tipe paket adalah Lifetime (seumur hidup).'),

                    DateTimePicker::make('grace_ends_at')
                        ->label('Batas Masa Tenggang (Grace Period)')
                        ->native(false)
                        ->seconds(false)
                        ->helperText('Periode toleransi setelah ends_at sebelum akses dicabut.'),

                    DateTimePicker::make('cancelled_at')
                        ->label('Tanggal Pembatalan')
                        ->native(false)
                        ->seconds(false),

                    Textarea::make('notes')
                        ->label('Catatan Admin / Komplimen')
                        ->placeholder('Alasan pemberian akses manual atau catatan khusus...')
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }
}
