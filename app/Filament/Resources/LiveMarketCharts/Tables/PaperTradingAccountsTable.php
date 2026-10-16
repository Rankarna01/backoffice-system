<?php

namespace App\Filament\Resources\LiveMarketCharts\Tables;

use App\Domain\Trading\Models\PaperTradingAccount;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaperTradingAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account_number')
                    ->label('No. Akun')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Nama Member')
                    ->searchable()
                    ->sortable()
                    ->description(fn (PaperTradingAccount $record): string => $record->user?->email ?? '-'),

                TextColumn::make('balance')
                    ->label('Saldo (USD)')
                    ->money('USD')
                    ->weight('bold')
                    ->color('success')
                    ->sortable(),

                TextColumn::make('equity')
                    ->label('Ekuitas (USD)')
                    ->money('USD')
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('open_positions_count')
                    ->label('Posisi Aktif')
                    ->counts('openPositions')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'gray'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'suspended' => 'danger',
                        'reset' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                // 1. Action: Atur Saldo (Set Saldo Akun Member)
                Action::make('set_balance')
                    ->label('Atur Saldo')
                    ->icon('bx-wallet')
                    ->color('success')
                    ->modalHeading(fn (PaperTradingAccount $record) => "Atur Saldo Trading: {$record->user?->name}")
                    ->modalDescription('Tentukan jumlah saldo simulasi baru untuk member ini.')
                    ->schema([
                        TextInput::make('balance')
                            ->label('Nominal Saldo Baru (USD)')
                            ->numeric()
                            ->required()
                            ->prefix('$')
                            ->default(fn (PaperTradingAccount $record) => $record->balance)
                            ->helperText('Contoh: 10000 untuk $10,000.00'),

                        Textarea::make('notes')
                            ->label('Catatan Admin')
                            ->placeholder('Alasan penyesuaian saldo (e.g. Bonus kelas SMC, reset kompetisi...)')
                            ->rows(2),
                    ])
                    ->action(function (PaperTradingAccount $record, array $data): void {
                        $newBalance = (float) $data['balance'];
                        $oldBalance = (float) $record->balance;

                        $record->balance = $newBalance;
                        $record->notes = $data['notes'] ?? $record->notes;
                        $record->recalculateEquity();

                        Notification::make()
                            ->title('Saldo Berhasil Diperbarui')
                            ->body("Saldo {$record->user?->name} diubah dari \${$oldBalance} menjadi \${$newBalance}.")
                            ->success()
                            ->send();
                    }),

                // 2. Action: Reset Saldo ke $10,000 Default
                Action::make('reset_balance')
                    ->label('Reset ($10,000)')
                    ->icon('bx-reset')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Saldo ke $10,000.00?')
                    ->modalDescription('Tindakan ini akan mengembalikan saldo ke $10,000.00 dan menutup seluruh posisi terbuka.')
                    ->action(function (PaperTradingAccount $record): void {
                        // Tutup semua open positions
                        foreach ($record->openPositions as $position) {
                            $position->close((float) ($position->current_price ?? $position->entry_price), 'admin_reset');
                        }

                        $record->balance = 10000.00;
                        $record->equity = 10000.00;
                        $record->status = 'active';
                        $record->notes = 'Saldo di-reset kembali ke nilai awal $10,000 oleh admin.';
                        $record->save();

                        Notification::make()
                            ->title('Akun Berhasil Direset')
                            ->body("Saldo akun {$record->account_number} telah dikembalikan ke $10,000.00.")
                            ->success()
                            ->send();
                    }),

                // 3. Action: Lihat Detail Posisi & Riwayat
                Action::make('view_positions')
                    ->label('Posisi & Riwayat')
                    ->icon('bx-list-ul')
                    ->color('primary')
                    ->modalHeading(fn (PaperTradingAccount $record) => "Portofolio Trading: {$record->user?->name} ({$record->account_number})")
                    ->modalContent(fn (PaperTradingAccount $record) => view('filament.resources.live-market-charts.user-positions-modal', [
                        'record' => $record,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalWidth('4xl'),
            ]);
    }
}
