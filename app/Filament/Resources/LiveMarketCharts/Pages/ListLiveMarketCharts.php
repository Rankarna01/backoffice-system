<?php

namespace App\Filament\Resources\LiveMarketCharts\Pages;

use App\Domain\Trading\Models\LiveChartConfig;
use App\Domain\Trading\Models\PaperTradingAccount;
use App\Filament\Resources\LiveMarketCharts\LiveMarketChartResource;
use App\Filament\Resources\LiveMarketCharts\Widgets\LiveMarketChartStatsWidget;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListLiveMarketCharts extends ListRecords
{
    protected static string $resource = LiveMarketChartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 1. Live Chart TradingView Modal Preview
            Action::make('preview_live_chart')
                ->label('Live Chart Embed (XAU/USD)')
                ->icon('bx-chart')
                ->color('warning')
                ->modalHeading('TradingView Live Market Chart Embed (XAU/USD)')
                ->modalDescription('Tampilan live chart interaktif real-time dengan candlestick, volume, dan drawing tools.')
                ->modalContent(function () {
                    $config = LiveChartConfig::getEffectiveConfig();
                    return view('filament.resources.live-market-charts.chart-embed-preview', [
                        'symbol' => $config->default_symbol ?: 'OANDA:XAUUSD',
                        'interval' => $config->default_interval ?: '60',
                        'theme' => $config->theme ?: 'dark',
                    ]);
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup Chart')
                ->modalWidth('7xl'),

            // 2. Setting / Konfigurasi Embed Chart
            Action::make('configure_chart')
                ->label('Pengaturan Chart & Default Saldo')
                ->icon('bx-cog')
                ->color('gray')
                ->modalHeading('Konfigurasi Global Live Chart & Akun Simulasi')
                ->modalDescription('Atur simbol utama, interval default, tema visual, serta saldo awal akun member.')
                ->schema([
                    TextInput::make('default_symbol')
                        ->label('Simbol Default (TradingView Code)')
                        ->default(fn () => LiveChartConfig::getEffectiveConfig()->default_symbol)
                        ->required()
                        ->helperText('Contoh: OANDA:XAUUSD, BINANCE:BTCUSDT, FX:EURUSD'),

                    Select::make('default_interval')
                        ->label('Timeframe Default')
                        ->options([
                            '1' => '1 Menit (1m)',
                            '5' => '5 Menit (5m)',
                            '15' => '15 Menit (15m)',
                            '30' => '30 Menit (30m)',
                            '60' => '1 Jam (1H)',
                            '240' => '4 Jam (4H)',
                            'D' => '1 Hari (1D)',
                            'W' => '1 Minggu (1W)',
                        ])
                        ->default(fn () => LiveChartConfig::getEffectiveConfig()->default_interval)
                        ->required(),

                    Select::make('theme')
                        ->label('Tema Tampilan')
                        ->options([
                            'dark' => 'Dark Theme (Sesuai Desain Gelap)',
                            'light' => 'Light Theme',
                        ])
                        ->default(fn () => LiveChartConfig::getEffectiveConfig()->theme)
                        ->required(),

                    TextInput::make('default_balance')
                        ->label('Saldo Awal Member Baru (USD)')
                        ->numeric()
                        ->prefix('$')
                        ->default(fn () => LiveChartConfig::getEffectiveConfig()->default_balance)
                        ->required(),

                    Toggle::make('allow_trading')
                        ->label('Izinkan Fitur Paper Trading Aktif')
                        ->default(fn () => LiveChartConfig::getEffectiveConfig()->allow_trading),
                ])
                ->action(function (array $data): void {
                    LiveChartConfig::updateOrCreate(
                        ['customer_id' => null],
                        [
                            'default_symbol' => $data['default_symbol'],
                            'default_interval' => $data['default_interval'],
                            'theme' => $data['theme'],
                            'default_balance' => (float) $data['default_balance'],
                            'allow_trading' => (bool) $data['allow_trading'],
                        ]
                    );

                    Notification::make()
                        ->title('Konfigurasi Chart Disimpan')
                        ->body('Pengaturan embed TradingView dan saldo default telah diperbarui.')
                        ->success()
                        ->send();
                }),

            // 3. Reset Semua Saldo Akun Member ke $10,000
            Action::make('bulk_reset')
                ->label('Reset Semua Akun ($10,000)')
                ->icon('bx-refresh')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Reset Seluruh Saldo Akun Member?')
                ->modalDescription('Semua akun trading simulasi akan dikembalikan saldonya ke $10,000.00.')
                ->action(function (): void {
                    PaperTradingAccount::query()->update([
                        'balance' => 10000.00,
                        'equity' => 10000.00,
                        'status' => 'active',
                    ]);

                    Notification::make()
                        ->title('Seluruh Akun Berhasil Direset')
                        ->body('Saldo semua akun trading member kini kembali ke $10,000.00.')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LiveMarketChartStatsWidget::class,
        ];
    }
}
