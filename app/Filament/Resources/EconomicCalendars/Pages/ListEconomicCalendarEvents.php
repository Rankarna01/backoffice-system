<?php

namespace App\Filament\Resources\EconomicCalendars\Pages;

use App\Domain\Market\Models\EconomicCalendarConfig;
use App\Filament\Resources\EconomicCalendars\EconomicCalendarResource;
use App\Filament\Resources\EconomicCalendars\Widgets\EconomicCalendarStatsWidget;
use App\Services\FcsApiService;
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
                ->label('⚙️ Pengaturan API Key (FCS API)')
                ->icon('heroicon-m-key')
                ->color('gray')
                ->modalHeading('Pengaturan API Access Key FCS API')
                ->modalDescription('Kelola API Access Key dari platform https://fcsapi.com/dashboard untuk kalender ekonomi forex, komoditas, dan indikator makro global.')
                ->fillForm(function () {
                    /** @var FcsApiService $service */
                    $service = app(FcsApiService::class);
                    $config = $service->getConfig();
                    return [
                        'api_key' => $config->api_key,
                        'base_url' => $config->base_url,
                        'is_active' => $config->is_active,
                    ];
                })
                ->form([
                    TextInput::make('api_key')
                        ->label('API Access Key FCS API (1 Kolom)')
                        ->placeholder('Contoh: AHw1wEDTk4Vqzyf3ElPTT3')
                        ->required()
                        ->helperText('Dapatkan API Key di https://fcsapi.com/dashboard pada bagian "API Access Key".'),

                    TextInput::make('base_url')
                        ->label('Base API Endpoint URL')
                        ->default('https://api-v4.fcsapi.com')
                        ->required()
                        ->helperText('Endpoint standar FCS API v4 (https://api-v4.fcsapi.com).'),

                    Toggle::make('is_active')
                        ->label('Aktifkan Provider FCS API')
                        ->default(true),
                ])
                ->action(function (array $data) {
                    /** @var FcsApiService $service */
                    $service = app(FcsApiService::class);
                    $config = $service->getConfig();
                    $config->update([
                        'api_key' => trim($data['api_key']),
                        'base_url' => trim($data['base_url']),
                        'is_active' => (bool) $data['is_active'],
                    ]);

                    Notification::make()
                        ->title('API Key Berhasil Disimpan!')
                        ->body('Konfigurasi FCS API telah diperbarui.')
                        ->success()
                        ->send();
                }),

            Action::make('test_api')
                ->label('⚡ Test Hubungi API')
                ->icon('heroicon-m-bolt')
                ->color('warning')
                ->modalHeading('Hasil Pengujian Koneksi FCS API (fcsapi.com)')
                ->modalContent(function () {
                    /** @var FcsApiService $service */
                    $service = app(FcsApiService::class);
                    $result = $service->testConnection();

                    $statusBadge = $result['success']
                        ? '<span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-500/20 text-emerald-400">✓ KONEKSI BERHASIL & AKTIF</span>'
                        : '<span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-500/20 text-rose-400">✕ GAGAL TERHUBUNG</span>';

                    $providerBadge = '<span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-indigo-500/20 text-indigo-400">Platform: FCS API v4</span>';

                    $sampleDataHtml = '';
                    if (! empty($result['sample_data'])) {
                        $sampleDataHtml = '<div class="mt-3"><p class="text-xs font-bold text-gray-300 mb-1">Cuplikan Respons Data Kalender Real-Time:</p><pre class="p-3 bg-gray-950 text-gray-300 rounded text-xs overflow-x-auto max-h-[160px]">' . e(json_encode($result['sample_data'], JSON_PRETTY_PRINT)) . '</pre></div>';
                    }

                    $creditInfo = isset($result['credit_count'])
                        ? '<span class="text-xs text-amber-400">Kredit Terpakai: ' . $result['credit_count'] . '</span>'
                        : '';

                    $serverTimeInfo = isset($result['server_time'])
                        ? '<span class="text-xs text-gray-400">Server Time: ' . e($result['server_time']) . '</span>'
                        : '';

                    return new HtmlString('
                        <div class="space-y-4">
                            <div class="flex flex-wrap items-center gap-2">
                                ' . $statusBadge . '
                                ' . $providerBadge . '
                                <span class="text-xs text-gray-400">Latensi: ' . ($result['latency_ms'] ?? 0) . ' ms</span>
                                ' . $creditInfo . '
                                ' . $serverTimeInfo . '
                            </div>
                            <div class="p-3 rounded-lg bg-gray-900 border border-gray-800 text-sm text-gray-200">
                                <p>' . e($result['message']) . '</p>
                            </div>
                            ' . $sampleDataHtml . '
                            <p class="text-xs text-gray-500">Endpoint: https://api-v4.fcsapi.com/forex/economy_cal</p>
                        </div>
                    ');
                }),

            Action::make('sync_live_data')
                ->label('🔄 Sinkronkan Data Live')
                ->icon('heroicon-m-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Sinkronisasi Kalender Ekonomi Live dari FCS API')
                ->modalDescription('Sistem akan mengambil data jadwal rilis ekonomi terbaru dari server FCS API dan menyimpannya ke tabel kalender.')
                ->action(function () {
                    /** @var FcsApiService $service */
                    $service = app(FcsApiService::class);
                    $count = $service->syncEvents();

                    if ($count > 0) {
                        Notification::make()
                            ->title('Sinkronisasi Berhasil!')
                            ->body("Berhasil memperbarui {$count} event kalender ekonomi dari FCS API.")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Sinkronisasi Belum Menghasilkan Data')
                            ->body('Pastikan API Key FCS API valid dan kuota request mencukupi.')
                            ->warning()
                            ->send();
                    }
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
