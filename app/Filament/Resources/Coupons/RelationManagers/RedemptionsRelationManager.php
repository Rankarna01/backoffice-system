<?php

namespace App\Filament\Resources\Coupons\RelationManagers;

use App\Domain\Billing\Models\CouponRedemption;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RedemptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'redemptions';

    protected static ?string $title = 'Riwayat Penggunaan Kupon (Redemptions)';

    protected static ?string $modelLabel = 'Penggunaan';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('redeemed_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Member / Pengguna')
                    ->weight('semibold')
                    ->description(fn (CouponRedemption $record): ?string => $record->user?->email)
                    ->searchable(),

                TextColumn::make('order.number')
                    ->label('Nomor Order')
                    ->weight('medium')
                    ->searchable(),

                TextColumn::make('discount_amount')
                    ->label('Nilai Potongan')
                    ->money('IDR', locale: 'id')
                    ->weight('bold'),

                TextColumn::make('redeemed_at')
                    ->label('Waktu Klaim')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ]);
    }
}
