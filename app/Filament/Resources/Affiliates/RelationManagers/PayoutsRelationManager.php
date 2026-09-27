<?php

namespace App\Filament\Resources\Affiliates\RelationManagers;

use App\Domain\Billing\Models\Payout;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PayoutsRelationManager extends RelationManager
{
    protected static string $relationship = 'payouts';

    protected static ?string $title = 'Riwayat Pencairan Dana (Payouts)';

    protected static ?string $modelLabel = 'Pencairan Dana';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('amount')
                ->label('Jumlah Dicairkan')
                ->numeric()
                ->prefix('Rp')
                ->required(),

            Select::make('method')
                ->label('Metode Transfer')
                ->options([
                    'bank_transfer' => 'Transfer Bank',
                    'ewallet' => 'E-Wallet',
                    'manual' => 'Manual Cash',
                ])
                ->default('bank_transfer')
                ->required(),

            TextInput::make('reference')
                ->label('Nomor Referensi Transfer')
                ->placeholder('Contoh: TRF-20260927-01')
                ->maxLength(100),

            Select::make('status')
                ->label('Status')
                ->options([
                    'pending' => 'Pending',
                    'paid' => 'Selesai (Paid)',
                    'failed' => 'Gagal',
                ])
                ->default('paid')
                ->required(),

            DateTimePicker::make('paid_at')
                ->label('Tanggal Transfer')
                ->default(now()),

            Textarea::make('note')
                ->label('Catatan Payout')
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('amount')
                    ->label('Jumlah Payout')
                    ->money('IDR', locale: 'id')
                    ->weight('bold'),

                TextColumn::make('method')
                    ->label('Metode')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bank_transfer' => 'Transfer Bank',
                        'ewallet' => 'E-Wallet',
                        default => 'Manual',
                    }),

                TextColumn::make('reference')
                    ->label('No. Ref')
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'failed' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid' => 'Selesai',
                        'failed' => 'Gagal',
                        default => 'Diproses',
                    }),

                TextColumn::make('paid_at')
                    ->label('Waktu Transfer')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('—'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Catat Payout Baru')
                    ->icon('bx-plus'),
            ]);
    }
}
