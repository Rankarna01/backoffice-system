<?php

namespace App\Filament\Resources\EconomicCalendars\Tables;

use App\Domain\Market\Models\EconomicCalendarEvent;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EconomicCalendarEventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('event_date', 'asc')
            ->columns([
                TextColumn::make('event_date')
                    ->label('Waktu Rilis')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->extraHeaderAttributes(['style' => 'min-width: 140px;'])
                    ->extraCellAttributes(['style' => 'min-width: 140px;']),

                TextColumn::make('currency')
                    ->label('Mata Uang')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'USD' => 'warning',
                        'EUR' => 'info',
                        'GBP' => 'primary',
                        'JPY' => 'danger',
                        default => 'success',
                    })
                    ->sortable()
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 100px;'])
                    ->extraCellAttributes(['style' => 'min-width: 100px;']),

                TextColumn::make('country')
                    ->label('Negara')
                    ->searchable()
                    ->sortable()
                    ->extraHeaderAttributes(['style' => 'min-width: 130px;'])
                    ->extraCellAttributes(['style' => 'min-width: 130px;']),

                TextColumn::make('event_name')
                    ->label('Peristiwa & Indikator Ekonomi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->grow(true)
                    ->description(fn (EconomicCalendarEvent $record): ?string => $record->period ? "Periode: {$record->period} • Sumber: {$record->source}" : "Sumber: {$record->source}")
                    ->extraHeaderAttributes(['style' => 'min-width: 300px;'])
                    ->extraCellAttributes(['style' => 'min-width: 300px;']),

                TextColumn::make('impact_level')
                    ->label('Dampak')
                    ->badge()
                    ->color(fn (EconomicCalendarEvent $record): string => $record->impact_badge_color)
                    ->formatStateUsing(fn (EconomicCalendarEvent $record): string => $record->impact_label)
                    ->sortable()
                    ->alignCenter()
                    ->extraHeaderAttributes(['style' => 'min-width: 120px;'])
                    ->extraCellAttributes(['style' => 'min-width: 120px;']),

                TextColumn::make('actual')
                    ->label('Aktual')
                    ->placeholder('-')
                    ->weight('bold')
                    ->alignRight()
                    ->formatStateUsing(fn ($state, EconomicCalendarEvent $record): string => filled($state) ? ($state . ($record->unit ? " {$record->unit}" : '')) : '-')
                    ->color(fn ($state) => filled($state) ? 'primary' : 'gray')
                    ->extraHeaderAttributes(['style' => 'min-width: 100px;'])
                    ->extraCellAttributes(['style' => 'min-width: 100px;']),

                TextColumn::make('forecast')
                    ->label('Konsensus')
                    ->placeholder('-')
                    ->alignRight()
                    ->formatStateUsing(fn ($state, EconomicCalendarEvent $record): string => filled($state) ? ($state . ($record->unit ? " {$record->unit}" : '')) : '-')
                    ->extraHeaderAttributes(['style' => 'min-width: 100px;'])
                    ->extraCellAttributes(['style' => 'min-width: 100px;']),

                TextColumn::make('previous')
                    ->label('Sebelumnya')
                    ->placeholder('-')
                    ->alignRight()
                    ->formatStateUsing(fn ($state, EconomicCalendarEvent $record): string => filled($state) ? ($state . ($record->unit ? " {$record->unit}" : '')) : '-')
                    ->extraHeaderAttributes(['style' => 'min-width: 100px;'])
                    ->extraCellAttributes(['style' => 'min-width: 100px;']),
            ])
            ->filters([
                SelectFilter::make('currency')
                    ->label('Mata Uang')
                    ->options([
                        'USD' => 'USD (United States Dollar)',
                        'EUR' => 'EUR (Euro)',
                        'GBP' => 'GBP (British Pound)',
                        'JPY' => 'JPY (Japanese Yen)',
                        'AUD' => 'AUD (Australian Dollar)',
                        'CAD' => 'CAD (Canadian Dollar)',
                        'CHF' => 'CHF (Swiss Franc)',
                    ]),

                SelectFilter::make('impact_level')
                    ->label('Tingkat Dampak Volatilitas')
                    ->options([
                        'high' => 'High Impact (Volatilitas Ekstrem 🔴)',
                        'medium' => 'Medium Impact 🟡',
                        'low' => 'Low Impact 🟢',
                    ]),

                Filter::make('today')
                    ->label('Hanya Rilis Hari Ini')
                    ->query(fn (Builder $query): Builder => $query->whereDate('event_date', today())),

                Filter::make('high_impact_only')
                    ->label('Hanya Dampak Merah (High Impact)')
                    ->query(fn (Builder $query): Builder => $query->where('impact_level', 'high')),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
