<?php

namespace App\Filament\Resources\EconomicCalendars\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EconomicCalendarEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Peristiwa & Indikator Ekonomi')
                    ->icon('heroicon-m-calendar-days')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('event_name')
                                    ->label('Nama Indikator / Peristiwa Rilis')
                                    ->placeholder('Contoh: Non Farm Payrolls (NFP), Core CPI YoY, Fed Rate Decision')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull()
                                    ->helperText('Nama indikator ekonomi yang dirilis bank sentral atau biro statistik.'),

                                TextInput::make('country')
                                    ->label('Negara Asal Rilis')
                                    ->placeholder('Contoh: United States, Euro Area, United Kingdom, Japan')
                                    ->required(),

                                Select::make('currency')
                                    ->label('Mata Uang Terdampak (Currency)')
                                    ->options([
                                        'USD' => 'USD (United States Dollar)',
                                        'EUR' => 'EUR (Euro)',
                                        'GBP' => 'GBP (British Pound)',
                                        'JPY' => 'JPY (Japanese Yen)',
                                        'AUD' => 'AUD (Australian Dollar)',
                                        'CAD' => 'CAD (Canadian Dollar)',
                                        'CHF' => 'CHF (Swiss Franc)',
                                        'NZD' => 'NZD (New Zealand Dollar)',
                                        'CNY' => 'CNY (Chinese Yuan)',
                                    ])
                                    ->default('USD')
                                    ->required()
                                    ->searchable(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('impact_level')
                                    ->label('Tingkat Dampak Volatilitas')
                                    ->options([
                                        'high' => 'High Impact (Volatilitas Ekstrem)',
                                        'medium' => 'Medium Impact (Pergerakan Sedang)',
                                        'low' => 'Low Impact (Sentimen Ringan)',
                                    ])
                                    ->default('high')
                                    ->required(),

                                DateTimePicker::make('event_date')
                                    ->label('Waktu Rilis Data')
                                    ->required()
                                    ->default(now()),
                            ]),

                        Section::make('Angka Indikator (Data Hasil)')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextInput::make('actual')
                                            ->label('Aktual (Hasil Rilis)')
                                            ->placeholder('Contoh: 254K atau 2.4%')
                                            ->helperText('Angka aktual saat rilis keluar.'),

                                        TextInput::make('forecast')
                                            ->label('Konsensus / Prediksi')
                                            ->placeholder('Contoh: 140K atau 2.3%')
                                            ->helperText('Estimasi rata-rata analis pasar.'),

                                        TextInput::make('previous')
                                            ->label('Sebelumnya (Previous)')
                                            ->placeholder('Contoh: 159K atau 2.5%')
                                            ->helperText('Angka data periode sebelumnya.'),

                                        TextInput::make('unit')
                                            ->label('Satuan Unit')
                                            ->placeholder('Contoh: %, K, B, Points'),
                                    ]),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('period')
                                    ->label('Periode Laporan')
                                    ->placeholder('Contoh: Sep 2024, Q3'),

                                TextInput::make('source')
                                    ->label('Sumber Penyedia Data')
                                    ->default('Trading Economics')
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
