<?php

namespace App\Filament\Resources\Billing\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('number')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('user.name')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->badge(),
                \Filament\Tables\Columns\TextColumn::make('total')->money('idr'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (\App\Domain\Billing\Models\Order $record) => $record->status === \App\Domain\Billing\Enums\OrderStatus::Pending)
                    ->action(function (\App\Domain\Billing\Models\Order $record) {
                        // Mark order as paid
                        $record->update(['status' => \App\Domain\Billing\Enums\OrderStatus::Paid, 'paid_at' => now()]);
                        
                        // Mark payment as paid
                        $record->payments()->update(['status' => 'paid']);

                        // Create Course Enrollment for each item
                        foreach ($record->items as $item) {
                            if ($item->purchasable_type === \App\Domain\Learning\Models\Course::class) {
                                \App\Domain\Learning\Models\CourseEnrollment::firstOrCreate([
                                    'user_id' => $record->user_id,
                                    'course_id' => $item->purchasable_id,
                                ], [
                                    'progress_percentage' => 0,
                                    'completed_lessons_count' => 0,
                                    'enrolled_at' => now(),
                                ]);
                            }
                        }

                        // Fire event for real-time notification
                        event(new \App\Events\OrderApproved($record));

                        \Filament\Notifications\Notification::make()
                            ->title('Order Approved')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
