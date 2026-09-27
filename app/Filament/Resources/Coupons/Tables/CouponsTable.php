<?php

namespace App\Filament\Resources\Coupons\Tables;

use App\Domain\Billing\Enums\CouponType;
use App\Domain\Billing\Models\Coupon;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('code')
                    ->label('Kode Kupon')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Kode kupon disalin!')
                    ->description(fn (Coupon $record): ?string => $record->name),

                TextColumn::make('discount')
                    ->label('Nilai Diskon')
                    ->state(fn (Coupon $record): string => $record->type === CouponType::Percent ? "{$record->value}%" : 'Rp ' . number_format($record->value, 0, ',', '.'))
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('min_order')
                    ->label('Min. Belanja')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                TextColumn::make('redemptions')
                    ->label('Pemakaian / Kuota')
                    ->state(function (Coupon $record): string {
                        $max = $record->max_redemptions > 0 ? $record->max_redemptions : '∞';
                        return "{$record->redemptions_count} / {$max}";
                    })
                    ->color('gray'),

                TextColumn::make('applies_to')
                    ->label('Cakupan')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'courses' => 'Kursus',
                        'plans' => 'Langganan',
                        default => 'Semua',
                    }),

                TextColumn::make('period')
                    ->label('Periode Berlaku')
                    ->state(function (Coupon $record): string {
                        if (! $record->ends_at) {
                            return 'Permanen';
                        }
                        return $record->ends_at->isPast() ? 'Kedaluwarsa' : 's/d ' . $record->ends_at->format('d M Y');
                    })
                    ->color(fn (Coupon $record): string => ($record->ends_at && $record->ends_at->isPast()) ? 'danger' : 'gray'),

                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Hanya Kupon Aktif'),

                SelectFilter::make('type')
                    ->label('Tipe Potongan')
                    ->options(CouponType::options()),

                SelectFilter::make('applies_to')
                    ->label('Cakupan Produk')
                    ->options([
                        'all' => 'Semua Produk',
                        'courses' => 'Kursus Satuan',
                        'plans' => 'Paket Langganan',
                    ]),
            ])
            ->recordActions([
                EditAction::make()->icon('bx-edit'),
                DeleteAction::make()->icon('bx-trash'),
            ]);
    }
}
