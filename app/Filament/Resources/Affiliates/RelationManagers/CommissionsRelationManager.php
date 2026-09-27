<?php

namespace App\Filament\Resources\Affiliates\RelationManagers;

use App\Domain\Billing\Models\Commission;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CommissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'commissions';

    protected static ?string $title = 'Riwayat Komisi Transaksi';

    protected static ?string $modelLabel = 'Komisi';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('order.number')
                    ->label('Nomor Order')
                    ->weight('semibold')
                    ->searchable(),

                TextColumn::make('order.user.name')
                    ->label('Pembeli / Referral')
                    ->description(fn (Commission $record): ?string => $record->order?->user?->email),

                TextColumn::make('amount')
                    ->label('Nilai Komisi')
                    ->money('IDR', locale: 'id')
                    ->weight('bold'),

                TextColumn::make('rate')
                    ->label('Rate')
                    ->state(fn (Commission $record): string => "{$record->rate}%"),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'approved' => 'info',
                        'void' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid' => 'Sudah Dicairkan',
                        'approved' => 'Disetujui',
                        'void' => 'Batal (Refund)',
                        default => 'Pending',
                    }),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ]);
    }
}
