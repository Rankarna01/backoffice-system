<?php

namespace App\Filament\Resources\Signals\Schemas;

use App\Domain\Market\Models\Signal;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SignalForm
{
    public static function configure(Schema $schema): Schema
    {
        $updateRiskReward = function (callable $get, callable $set) {
            $action = (string) ($get('action') ?? 'BUY');
            $entry = (float) ($get('entry_price') ?? 0);
            $sl = (float) ($get('stop_loss') ?? 0);
            $tp = (float) ($get('take_profit_1') ?? 0);

            $computed = Signal::computeRiskReward($action, $entry, $sl, $tp);
            if ($computed) {
                $set('risk_reward_ratio', $computed);
            }
        };

        return $schema
            ->components([
                Tabs::make('Signal Details')
                    ->tabs([
                        // Tab 1: Setup Market & Instrumen
                        Tab::make('Setup Market & Instrumen')
                            ->icon('heroicon-m-globe-alt')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Judul Setup Sinyal')
                                            ->placeholder('Contoh: XAUUSD Bearish Institutional Supply Rejection')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull()
                                            ->helperText('Judul teknis setup yang akan tampil di notifikasi push dan dashboard member.'),

                                        Select::make('market_type')
                                            ->label('Jenis Kategori Market')
                                            ->options([
                                                'forex' => 'Forex Currencies (EUR/USD, GBP/USD, dll)',
                                                'commodities' => 'Komoditas & Logam (XAU/USD Gold, Oil)',
                                                'crypto' => 'Kripto Derivatif (BTC/USDT, ETH/USDT)',
                                                'indices' => 'Indeks Saham Global (US30, NAS100, SPX)',
                                            ])
                                            ->default('commodities')
                                            ->required()
                                            ->live()
                                            ->helperText('Pilih rumpun pasar instrumen keuangan.'),

                                        Select::make('pair')
                                            ->label('Simbol Instrumen / Pair')
                                            ->options(fn (callable $get) => match ($get('market_type')) {
                                                'commodities' => [
                                                    'XAUUSD' => 'XAU/USD (Gold / Emas Spot)',
                                                    'XAGUSD' => 'XAG/USD (Silver / Perak)',
                                                    'USOIL' => 'USOIL (WTI Crude Oil)',
                                                    'UKOIL' => 'UKOIL (Brent Crude Oil)',
                                                ],
                                                'crypto' => [
                                                    'BTCUSDT' => 'BTC/USDT (Bitcoin)',
                                                    'ETHUSDT' => 'ETH/USDT (Ethereum)',
                                                    'SOLUSDT' => 'SOL/USDT (Solana)',
                                                    'BNBUSDT' => 'BNB/USDT (BNB Chain)',
                                                    'XRPUSDT' => 'XRP/USDT (Ripple)',
                                                ],
                                                'indices' => [
                                                    'US30' => 'US30 (Dow Jones Industrial)',
                                                    'NAS100' => 'NAS100 (Nasdaq 100 Tech)',
                                                    'SPX500' => 'SPX500 (S&P 500 Index)',
                                                    'GER40' => 'GER40 (Germany DAX 40)',
                                                    'UK100' => 'UK100 (FTSE 100 Index)',
                                                ],
                                                default => [
                                                    'EURUSD' => 'EUR/USD (Euro vs US Dollar)',
                                                    'GBPUSD' => 'GBP/USD (Poundsterling vs USD)',
                                                    'USDJPY' => 'USD/JPY (US Dollar vs Japanese Yen)',
                                                    'AUDUSD' => 'AUD/USD (Australian Dollar vs USD)',
                                                    'USDCAD' => 'USD/CAD (US Dollar vs Canadian Dollar)',
                                                    'USDCHF' => 'USD/CHF (US Dollar vs Swiss Franc)',
                                                    'NZDUSD' => 'NZD/USD (New Zealand Dollar vs USD)',
                                                    'EURGBP' => 'EUR/GBP (Euro vs British Pound)',
                                                    'GBPJPY' => 'GBP/JPY (Pound vs Yen - Dragon)',
                                                ],
                                            })
                                            ->searchable()
                                            ->required()
                                            ->helperText('Simbol ticker pair yang ditransaksikan.'),

                                        Select::make('timeframe')
                                            ->label('Timeframe Analisa')
                                            ->options([
                                                'M5' => 'M5 (Scalping Cepat)',
                                                'M15' => 'M15 (Day Trading / Intra-session)',
                                                'H1' => 'H1 (Hourly Swing Execution)',
                                                'H4' => 'H4 (H4 Structure & Bias)',
                                                'D1' => 'D1 (Daily Macro Swing)',
                                            ])
                                            ->default('H1')
                                            ->required(),

                                        Select::make('mentor_id')
                                            ->label('Analis / Mentor Penerbit')
                                            ->relationship('mentor', 'name', fn ($query) => $query->whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'mentor'])))
                                            ->searchable()
                                            ->preload()
                                            ->default(fn () => auth()->id())
                                            ->helperText('Mentor atau analis institusi yang membagikan sinyal.'),

                                        Toggle::make('is_premium')
                                            ->label('Khusus Member VIP / Premium')
                                            ->default(true)
                                            ->helperText('Jika aktif, sinyal hanya dapat dilihat oleh member dengan subscription aktif.')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // Tab 2: Parameter Harga & Manajemen Risiko
                        Tab::make('Parameter Harga & R:R')
                            ->icon('heroicon-m-calculator')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Select::make('action')
                                            ->label('Tipe Order & Arah')
                                            ->options([
                                                'BUY' => 'BUY (Market Execution)',
                                                'SELL' => 'SELL (Market Execution)',
                                                'BUY_LIMIT' => 'BUY LIMIT (Pending Order)',
                                                'SELL_LIMIT' => 'SELL LIMIT (Pending Order)',
                                                'BUY_STOP' => 'BUY STOP (Breakout)',
                                                'SELL_STOP' => 'SELL STOP (Breakout)',
                                            ])
                                            ->default('BUY')
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated($updateRiskReward),

                                        TextInput::make('entry_price')
                                            ->label('Harga Entry (Masuk Posisi)')
                                            ->numeric()
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated($updateRiskReward)
                                            ->helperText('Level harga eksekusi posisi.'),

                                        TextInput::make('stop_loss')
                                            ->label('Stop Loss (SL Proteksi)')
                                            ->numeric()
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated($updateRiskReward)
                                            ->helperText('Level batasan risiko kerugian.'),

                                        TextInput::make('take_profit_1')
                                            ->label('Take Profit 1 (TP1 Utama)')
                                            ->numeric()
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated($updateRiskReward)
                                            ->helperText('Target keuntungan pertama (Secure Profit).'),

                                        TextInput::make('take_profit_2')
                                            ->label('Take Profit 2 (TP2 Runner)')
                                            ->numeric()
                                            ->placeholder('Opsional')
                                            ->helperText('Target kedua untuk posisi partial running.'),

                                        TextInput::make('take_profit_3')
                                            ->label('Take Profit 3 (TP3 Extended)')
                                            ->numeric()
                                            ->placeholder('Opsional')
                                            ->helperText('Target swing terjauh (Macro POI).'),

                                        TextInput::make('risk_reward_ratio')
                                            ->label('Risk to Reward Ratio (R:R)')
                                            ->placeholder('Contoh: 1:3.0')
                                            ->helperText('Dihitung otomatis atau dapat disesuaikan manual.'),

                                        TextInput::make('risk_percentage')
                                            ->label('Rekomendasi Risiko Modal')
                                            ->numeric()
                                            ->default(1.0)
                                            ->suffix('%')
                                            ->required()
                                            ->helperText('Persentase modal akun yang direkomendasikan.'),
                                    ]),
                            ]),

                        // Tab 3: Status Hasil & Analisa Chart
                        Tab::make('Status & Analisa Chart')
                            ->icon('heroicon-m-chart-bar-square')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('status')
                                            ->label('Status Sinyal Saat Ini')
                                            ->options([
                                                'pending' => 'Pending Order (Menunggu Penjemputan)',
                                                'active' => 'Active Running (Order Terbuka)',
                                                'hit_tp1' => 'Hit TP 1 (Target Pertama Tercapai)',
                                                'hit_tp2' => 'Hit TP 2 (Target Kedua Tercapai)',
                                                'hit_tp3' => 'Hit TP 3 (Target Maksimal Tercapai)',
                                                'hit_sl' => 'Hit Stop Loss (Kena SL Proteksi)',
                                                'closed' => 'Closed (Tutup Manual / BEP)',
                                                'cancelled' => 'Cancelled (Order Dibatalkan)',
                                            ])
                                            ->default('active')
                                            ->required(),

                                        TextInput::make('result_pips')
                                            ->label('Hasil Perolehan Pips / Poin')
                                            ->numeric()
                                            ->placeholder('Contoh: +120 atau -30')
                                            ->helperText('Isi hasil pips setelah trade selesai (positif untuk profit, negatif untuk loss).'),

                                        TextInput::make('chart_image_url')
                                            ->label('URL Screenshot Chart (TradingView / R2 CDN)')
                                            ->placeholder('https://www.tradingview.com/x/... atau https://pub-r2.tradingedu.dev/...')
                                            ->columnSpanFull()
                                            ->helperText('Tautan gambar visual analisa teknikal untuk panduan visual murid.'),

                                        Textarea::make('analysis_notes')
                                            ->label('Catatan Analisa Teknikal & Trigger Konfirmasi')
                                            ->placeholder("Contoh:\n- Liquidity Sweep pada Asian Session High\n- Terbentuk M5 CHoCH & FVG Mitigation\n- Risk minimal dengan sniper entry pada 50% Order Block")
                                            ->rows(4)
                                            ->columnSpanFull()
                                            ->helperText('Uraian alasan logis entry agar murid sekaligus belajar studi kasus nyata.'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
