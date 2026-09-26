<?php

namespace App\Filament\Resources\EconomicCalendars\Tables;

use App\Domain\Market\Models\WidgetConfig;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class WidgetConfigsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.name')
                    ->label('Lingkup / Target Customer')
                    ->default('🌐 Global Default (Semua Customer)')
                    ->badge()
                    ->color(fn (WidgetConfig $record): string => $record->customer_id ? 'primary' : 'success')
                    ->description(fn (WidgetConfig $record): ?string => $record->customer?->email)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('color_theme')
                    ->label('Tema')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'dark' ? 'gray' : 'warning')
                    ->formatStateUsing(fn (string $state): string => $state === 'dark' ? 'Dark 🌙' : 'Light ☀️'),

                TextColumn::make('dimensions')
                    ->label('Ukuran (W × H)')
                    ->state(fn (WidgetConfig $record): string => "{$record->width} × {$record->height}px")
                    ->color('gray'),

                TextColumn::make('locale')
                    ->label('Bahasa')
                    ->badge()
                    ->color('info'),

                TextColumn::make('importance_filter')
                    ->label('Dampak Rilis')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        '1' => 'danger',
                        '0,1' => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        '1' => 'High Only 🔴',
                        '0,1' => 'Med & High 🟡🔴',
                        default => 'Semua Dampak 🟢🟡🔴',
                    }),

                TextColumn::make('currencies')
                    ->label('Mata Uang')
                    ->badge()
                    ->separator(',')
                    ->limitList(3),

                ToggleColumn::make('is_active')
                    ->label('Status Aktif'),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('customer_id', 'asc')
            ->recordActions([
                Action::make('preview')
                    ->label('Preview Widget')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('warning')
                    ->modalHeading(fn (WidgetConfig $record): string => 'Live Preview: ' . ($record->customer_id ? "Customer: {$record->customer?->name}" : 'Global Default'))
                    ->modalContent(fn (WidgetConfig $record) => view('filament.resources.economic-calendars.preview-widget', [
                        'config' => $record,
                        'tradingViewConfig' => $record->toTradingViewConfig(),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup Preview')
                    ->modalWidth('5xl'),

                EditAction::make(),

                DeleteAction::make()
                    ->visible(fn (WidgetConfig $record): bool => $record->customer_id !== null),
            ]);
    }
}
