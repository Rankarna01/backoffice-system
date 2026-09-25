<?php

namespace App\Filament\Resources\EconomicCalendars\Pages;

use App\Domain\Market\Models\EconomicCalendarConfig;
use App\Filament\Resources\EconomicCalendars\EconomicCalendarResource;
use App\Filament\Resources\EconomicCalendars\Widgets\EconomicCalendarStatsWidget;
use App\Services\TradingEconomicsService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\HtmlString;

class ListEconomicCalendarEvents extends ListRecords
{
    protected static string $resource = EconomicCalendarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manage_api_key')
                ->label('⚙️ Pengaturan API Key')
                ->icon('heroicon-m-key')
                ->color('gray')
                ->modalHeading('Pengaturan API Key Trading Economics')
                ->modalDescription('Masukkan API Key yang Anda peroleh dari https://tradingeconomics.com/api/. Anda juga dapat menggunakan demo key guest:guest untuk pengujian cepat.')
                ->fillForm(function () {
                    /** @var TradingEconomicsService $service */
                    $service = app(TradingEconomicsService::class);
                    $config = $service->getConfig();
                    return [
                        'api_key' => $config->api_key,
                        'base_url' => $config->base_url,
                        'is_active' => $config->is_active,
                    ];
                })
                ->form([
                    TextInput::make('api_key')
                        ->label('API Key Trading Economics (1 Kolom)')
                        ->placeholder('guest:guest atau masukkan API Key pribadi Anda')
                        ->required()
                        ->helperText('Daftar gratis di https://tradingeconomics.com/api/ untuk mendapatkan API key pribadi resmi.'),

                    TextInput::make('base_url')
                        ->label('Base API Endpoint URL')
                        ->default('https://api.tradingeconomics.com')
                        ->required(),

                    Toggle::make('is_active')
                        ->label('Aktifkan Provider Ini')
                        ->default(true),
                ])
                ->action(function (array $data) {
                    /** @var TradingEconomicsService $service */
                    $service = app(TradingEconomicsService::class);
                    $config = $service->getConfig();
                    $config->update([
                        'api_key' => trim($data['api_key']),
                        'base_url' => trim($data['base_url']),
                        'is_active' => (bool) $data['is_active'],
                    ]);

                    Notification::make()
                        ->title('API Key Berhasil Disimpan!')
                        ->body('Konfigurasi Trading Economics telah diperbarui.')
                        ->success()
                        ->send();
                }),

            Action::make('test_api')
                ->label('⚡ Test Hubungi API')
                ->icon('heroicon-m-bolt')
                ->color('warning')
                ->modalHeading('Hasil Pengujian Koneksi Trading Economics API')
                ->modalContent(function () {
                    /** @var TradingEconomicsService $service */
                    $service = app(TradingEconomicsService::class);
                    $result = $service->testConnection();

                    $statusBadge = $result['success']
                        ? '<span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-success-500/20 text-success-400">✓ KONEKSI BERHASIL</span>'
                        : '<span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-danger-500/20 text-danger-400">✕ GAGAL TERHUBUNG</span>';

                    $modeBadge = ($result['is_demo'] ?? false)
                        ? '<span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-warning-500/20 text-warning-400">Mode Demo Key (guest:guest)</span>'
                        : '<span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-info-500/20 text-info-400">Live API Key Pribadi</span>';

                    $sampleDataHtml = '';
                    if (! empty($result['sample_data'])) {
                        $sampleDataHtml = '<div class="mt-3"><p class="text-xs font-bold text-gray-300 mb-1">Cuplikan Respons Data Kalender:</p><pre class="p-3 bg-gray-950 text-gray-300 rounded text-xs overflow-x-auto max-h-[160px]">' . e(json_encode($result['sample_data'], JSON_PRETTY_PRINT)) . '</pre></div>';
                    }

                    return new HtmlString('
                        <div class="space-y-4">
                            <div class="flex items-center gap-2">
                                ' . $statusBadge . '
                                ' . $modeBadge . '
                                <span class="text-xs text-gray-400">Latensi: ' . ($result['latency_ms'] ?? 0) . ' ms</span>
                            </div>
                            <div class="p-3 rounded-lg bg-gray-900 border border-gray-800 text-sm text-gray-200">
                                <p>' . e($result['message']) . '</p>
                            </div>
                            ' . $sampleDataHtml . '
                            <p class="text-xs text-gray-500">Endpoint: https://api.tradingeconomics.com/calendar</p>
                        </div>
                    ');
                }),

            CreateAction::make()
                ->label('Tambah Event Manual')
                ->icon('heroicon-m-plus-circle'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EconomicCalendarStatsWidget::class,
        ];
    }
}
