<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use App\Domain\Billing\Enums\SubscriptionStatus;
use App\Domain\Billing\Models\Plan;
use App\Domain\Billing\Models\Subscription;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Member')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (Subscription $record): ?string => $record->user?->email),

                TextColumn::make('plan.name')
                    ->label('Paket Langganan')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state instanceof SubscriptionStatus ? $state->getColor() : 'gray')
                    ->formatStateUsing(fn ($state) => $state instanceof SubscriptionStatus ? $state->getLabel() : (string) $state)
                    ->icon(fn ($state) => $state instanceof SubscriptionStatus ? $state->getIcon() : null)
                    ->sortable(),

                TextColumn::make('starts_at')
                    ->label('Mulai')
                    ->dateTime('d M Y')
                    ->sortable(),

                TextColumn::make('ends_at')
                    ->label('Berakhir')
                    ->state(fn (Subscription $record): string => $record->ends_at ? $record->ends_at->format('d M Y') : 'Lifetime')
                    ->description(function (Subscription $record): ?string {
                        if (! $record->ends_at) {
                            return 'Akses seumur hidup';
                        }
                        $days = $record->remainingDays();
                        return $days !== null ? ($days > 0 ? "Sisa {$days} hari" : 'Sudah berakhir') : null;
                    })
                    ->sortable(),

                IconColumn::make('auto_renew')
                    ->label('Auto-Renew')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Akses')
                    ->options(SubscriptionStatus::options()),

                SelectFilter::make('plan_id')
                    ->label('Paket')
                    ->options(fn () => Plan::pluck('name', 'id')),

                TernaryFilter::make('is_lifetime')
                    ->label('Member Lifetime')
                    ->queries(
                        true: fn ($query) => $query->whereNull('ends_at'),
                        false: fn ($query) => $query->whereNotNull('ends_at'),
                    ),
            ])
            ->recordActions([
                Action::make('extend_30')
                    ->label('+30 Hari')
                    ->icon('bx-plus-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Perpanjang Masa Aktif 30 Hari')
                    ->modalDescription('Apakah Anda ingin menambahkan 30 hari ke masa aktif langganan ini?')
                    ->action(function (Subscription $record) {
                        $baseDate = ($record->ends_at && $record->ends_at->isFuture()) ? $record->ends_at : now();
                        $record->update([
                            'ends_at' => $baseDate->copy()->addDays(30),
                            'status' => SubscriptionStatus::Active,
                        ]);

                        Notification::make()
                            ->title('Masa Aktif Diperpanjang 30 Hari')
                            ->success()
                            ->send();
                    }),

                Action::make('cancel_subscription')
                    ->label('Batalkan')
                    ->icon('bx-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Subscription $record): bool => $record->status === SubscriptionStatus::Active)
                    ->action(function (Subscription $record) {
                        $record->update([
                            'status' => SubscriptionStatus::Cancelled,
                            'cancelled_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Langganan Dibatalkan')
                            ->warning()
                            ->send();
                    }),

                EditAction::make()->icon('bx-edit'),
                DeleteAction::make()->icon('bx-trash'),
            ]);
    }
}
