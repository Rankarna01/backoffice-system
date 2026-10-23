<?php

namespace App\Filament\Widgets;

use App\Domain\Billing\Enums\OrderStatus;
use App\Domain\Billing\Models\Order;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrdersWidget extends BaseWidget
{
    protected static ?string $heading = 'Pesanan Terbaru (Latest Orders)';

    protected static ?int $sort = 4;

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = [
        'default' => 1,
        'xl' => 2,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->with(['user', 'items'])
                    ->latest('created_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('No. Order')
                    ->searchable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Nomor order disalin!'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->weight('medium')
                    ->description(fn (Order $record): ?string => $record->user?->email)
                    ->placeholder('Guest / User'),

                Tables\Columns\TextColumn::make('items_summary')
                    ->label('Item Pesanan')
                    ->state(function (Order $record): string {
                        $items = $record->items->pluck('name');
                        return $items->isNotEmpty() ? $items->join(', ') : 'Paket Langganan';
                    })
                    ->limit(24)
                    ->color('gray'),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total Tagihan')
                    ->money('IDR', locale: 'id')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state instanceof OrderStatus ? $state->getColor() : 'gray')
                    ->formatStateUsing(fn ($state) => $state instanceof OrderStatus ? $state->getLabel() : (string) $state)
                    ->icon(fn ($state) => $state instanceof OrderStatus ? $state->getIcon() : null),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i')
                    ->color('gray')
                    ->sortable(),
            ])
            ->paginated([5])
            ->defaultPaginationPageOption(5)
            ->actions([
                Action::make('view')
                    ->label('Detail')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->url(fn (Order $record): string => "/admin/orders/{$record->id}/edit"),
            ])
            ->headerActions([
                Action::make('view_all')
                    ->label('Buka Semua Pesanan')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('primary')
                    ->url('/admin/orders'),
            ]);
    }
}
