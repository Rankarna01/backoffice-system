<?php

namespace App\Filament\Resources\Packages\Tables;

use App\Domain\Billing\Models\Plan;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Paket & Penawaran')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(function (Plan $record): string {
                        $parts = [];
                        if ($record->badge) {
                            $parts[] = "★ {$record->badge}";
                        }
                        $parts[] = $record->type === 'lifetime' ? 'Akses Seumur Hidup' : "{$record->duration_days} Hari";
                        return implode(' • ', $parts);
                    }),

                TextColumn::make('tier')
                    ->label('Tingkatan (Tier)')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'free' => 'gray',
                        'standard' => 'info',
                        'pro' => 'warning',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'free' => 'FREE',
                        'standard' => 'STANDARD',
                        'pro' => 'PRO VIP',
                        default => strtoupper($state),
                    })
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Harga')
                    ->state(function (Plan $record): string {
                        if ($record->isFree()) {
                            return 'GRATIS (Rp 0)';
                        }
                        $formatted = 'Rp ' . number_format((float) $record->price, 0, ',', '.');
                        if ($record->compare_at_price && $record->compare_at_price > $record->price) {
                            $old = 'Rp ' . number_format((float) $record->compare_at_price, 0, ',', '.');
                            return "{$formatted} (Coret: {$old})";
                        }
                        return $formatted;
                    })
                    ->badge(fn (Plan $record) => $record->isFree())
                    ->color(fn (Plan $record) => $record->isFree() ? 'success' : 'gray')
                    ->sortable(),

                TextColumn::make('courses_access')
                    ->label('Cakupan Kursus')
                    ->state(function (Plan $record): string {
                        if ($record->includes_all_courses) {
                            return 'Semua Kursus (All-Access)';
                        }
                        $count = $record->courses()->count();
                        return $count > 0 ? "{$count} Kursus Terpilih" : 'Belum Ditentukan';
                    })
                    ->badge()
                    ->color(fn (Plan $record) => $record->includes_all_courses ? 'success' : 'primary'),

                IconColumn::make('includes_signals')
                    ->label('Sinyal VIP')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('subscriptions_count')
                    ->label('Pelanggan Aktif')
                    ->counts('subscriptions')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Status Aktif')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('tier')
                    ->label('Tingkatan (Tier)')
                    ->options([
                        'free' => 'Free (Gratis)',
                        'standard' => 'Standard (Reguler)',
                        'pro' => 'Pro (VIP)',
                    ]),

                SelectFilter::make('type')
                    ->label('Tipe Durasi')
                    ->options([
                        'duration' => 'Durasi (Hari)',
                        'lifetime' => 'Lifetime',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->trueLabel('Hanya Paket Aktif')
                    ->falseLabel('Hanya Paket Nonaktif'),
            ])
            ->actions([
                Action::make('preview')
                    ->label('Lihat Katalog')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('gray')
                    ->url('/checkout/pro')
                    ->openUrlInNewTab(),

                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
