<?php

namespace App\Filament\Resources\Affiliates\Tables;

use App\Domain\Billing\Enums\AffiliateStatus;
use App\Domain\Billing\Models\Affiliate;
use App\Domain\Billing\Models\Payout;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AffiliatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Mitra')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (Affiliate $record): ?string => $record->user?->email),

                TextColumn::make('code')
                    ->label('Kode Referral')
                    ->badge()
                    ->color('primary')
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Kode referral disalin!')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('commission_rate')
                    ->label('Komisi')
                    ->state(fn (Affiliate $record): string => "{$record->commission_rate}%")
                    ->sortable(),

                TextColumn::make('total_earnings')
                    ->label('Total Komisi')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                TextColumn::make('unpaid_balance')
                    ->label('Saldo Belum Cair')
                    ->state(fn (Affiliate $record): float => $record->unpaidBalance())
                    ->money('IDR', locale: 'id')
                    ->weight('bold')
                    ->color(fn (float $state): string => $state > 0 ? 'success' : 'gray'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state instanceof AffiliateStatus ? $state->getColor() : 'gray')
                    ->formatStateUsing(fn ($state) => $state instanceof AffiliateStatus ? $state->getLabel() : (string) $state)
                    ->icon(fn ($state) => $state instanceof AffiliateStatus ? $state->getIcon() : null)
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Bergabung')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Mitra')
                    ->options(AffiliateStatus::options()),
            ])
            ->recordActions([
                Action::make('approve_affiliate')
                    ->label('Setujui')
                    ->icon('bx-check-shield')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Affiliate $record): bool => $record->status === AffiliateStatus::Pending)
                    ->action(function (Affiliate $record) {
                        $record->update([
                            'status' => AffiliateStatus::Approved,
                            'approved_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Mitra Afiliasi Disetujui')
                            ->success()
                            ->send();
                    }),

                Action::make('payout_balance')
                    ->label('Cairkan Saldo')
                    ->icon('bx-money')
                    ->color('primary')
                    ->visible(fn (Affiliate $record): bool => $record->unpaidBalance() > 0)
                    ->form([
                        TextInput::make('amount')
                            ->label('Nominal Pencairan')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(fn (Affiliate $record) => $record->unpaidBalance())
                            ->required(),

                        Select::make('method')
                            ->label('Metode Pencairan')
                            ->options([
                                'bank_transfer' => 'Transfer Bank',
                                'ewallet' => 'E-Wallet',
                            ])
                            ->default('bank_transfer')
                            ->required(),

                        TextInput::make('reference')
                            ->label('Nomor Bukti Transfer')
                            ->placeholder('Contoh: TRF-BCA-88992'),

                        Textarea::make('note')
                            ->label('Catatan Payout')
                            ->placeholder('Keterangan pembayaran komisi...'),
                    ])
                    ->action(function (Affiliate $record, array $data) {
                        Payout::create([
                            'affiliate_id' => $record->id,
                            'amount' => $data['amount'],
                            'method' => $data['method'],
                            'reference' => $data['reference'] ?? null,
                            'status' => 'paid',
                            'paid_at' => now(),
                            'note' => $data['note'] ?? null,
                        ]);

                        Notification::make()
                            ->title('Pencairan Komisi Berhasil Dicatat')
                            ->success()
                            ->send();
                    }),

                EditAction::make()->icon('bx-edit'),
                DeleteAction::make()->icon('bx-trash'),
            ]);
    }
}
