<?php

namespace App\Filament\Resources\Billing\Orders\Tables;

use App\Domain\Billing\Enums\OrderStatus;
use App\Domain\Billing\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('number')
                    ->label('Nomor Order')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Nomor order disalin!'),

                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (Order $record): ?string => $record->user?->email)
                    ->placeholder('Guest / User'),

                TextColumn::make('items_summary')
                    ->label('Item Pesanan')
                    ->state(function (Order $record): string {
                        $items = $record->items->pluck('name');
                        return $items->isNotEmpty() ? $items->join(', ') : 'Paket Langganan';
                    })
                    ->limit(35)
                    ->color('gray'),

                TextColumn::make('total')
                    ->label('Total Tagihan')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state instanceof OrderStatus ? $state->getColor() : 'gray')
                    ->formatStateUsing(fn ($state) => $state instanceof OrderStatus ? $state->getLabel() : (string) $state)
                    ->icon(fn ($state) => $state instanceof OrderStatus ? $state->getIcon() : null)
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Order')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pesanan')
                    ->options(OrderStatus::options()),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui (Approve)')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pelunasan & Aktivasi Akses')
                    ->modalDescription('Tandai pesanan ini lunas dan aktifkan akses kursus/langganan untuk member?')
                    ->visible(fn (Order $record) => $record->status === OrderStatus::Pending)
                    ->action(function (Order $record) {
                        // Mark order as paid
                        $record->update([
                            'status' => OrderStatus::Paid,
                            'paid_at' => now(),
                        ]);
                        
                        // Mark payment as paid
                        $record->payments()->update(['status' => 'paid']);

                        // Create Course Enrollment for each item
                        foreach ($record->items as $item) {
                            if ($item->purchasable_type === \App\Domain\Learning\Models\Course::class) {
                                \App\Domain\Learning\Models\CourseEnrollment::firstOrCreate([
                                    'user_id' => $record->user_id,
                                    'course_id' => $item->purchasable_id,
                                    'order_id' => $record->id,
                                ], [
                                    'progress_percentage' => 0,
                                    'completed_lessons_count' => 0,
                                    'enrolled_at' => now(),
                                ]);
                            }
                        }

                        // Fire event for real-time notification
                        if (class_exists(\App\Events\OrderApproved::class)) {
                            event(new \App\Events\OrderApproved($record));
                        }

                        Notification::make()
                            ->title('Pesanan Disetujui (Approved)')
                            ->body("Pesanan #{$record->number} telah lunas dan akses materi telah aktif.")
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
