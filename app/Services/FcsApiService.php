<?php

namespace App\Services;

use App\Domain\Market\Models\EconomicCalendarConfig;
use App\Domain\Market\Models\EconomicCalendarEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcsApiService
{
    public const PROVIDER_KEY = 'fcsapi';
    public const DEFAULT_BASE_URL = 'https://api-v4.fcsapi.com';
    public const DEFAULT_API_KEY = 'AHw1wEDTk4Vqzyf3ElPTT3';

    /**
     * Get or create FCS API configuration record.
     */
    public function getConfig(): EconomicCalendarConfig
    {
        return EconomicCalendarConfig::firstOrCreate(
            ['provider' => self::PROVIDER_KEY],
            [
                'name' => 'FCS API (fcsapi.com)',
                'api_key' => self::DEFAULT_API_KEY,
                'base_url' => self::DEFAULT_BASE_URL,
                'status' => 'ready',
                'is_active' => true,
            ]
        );
    }

    /**
     * Test connection to FCS API using specified or stored API key.
     */
    public function testConnection(?string $apiKey = null): array
    {
        $config = $this->getConfig();
        $key = $apiKey ?: ($config->api_key ?: self::DEFAULT_API_KEY);
        $baseUrl = rtrim($config->base_url ?: self::DEFAULT_BASE_URL, '/');
        $endpoint = "{$baseUrl}/forex/economy_cal?access_key=" . urlencode($key);

        $startTime = microtime(true);

        try {
            $response = Http::timeout(8)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'CourseTradingBackoffice/1.0',
                ])
                ->get($endpoint);

            $latency = (int) round((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $payload = $response->json();

                // FCS API returns { status: true, code: 200, response: [...], info: {...} }
                if (isset($payload['status']) && $payload['status'] === true) {
                    $events = $payload['response'] ?? [];
                    $sample = is_array($events) ? array_slice($events, 0, 3) : [];

                    $config->update([
                        'api_key' => $key,
                        'status' => 'connected',
                        'last_tested_at' => now(),
                        'last_error_message' => null,
                    ]);

                    return [
                        'success' => true,
                        'status_code' => $response->status(),
                        'latency_ms' => $latency,
                        'credit_count' => $payload['info']['credit_count'] ?? null,
                        'server_time' => $payload['info']['server_time'] ?? null,
                        'message' => 'Koneksi Berhasil! API Key FCS API valid dan aktif menerima data kalender ekonomi live.',
                        'total_events_retrieved' => count($events),
                        'sample_data' => $sample,
                    ];
                }

                // If FCS API returned status: false (e.g. invalid key code: 101)
                $errMsg = $payload['msg'] ?? 'Respons FCS API menyatakan kegagalan autentikasi / status false.';
                $config->update([
                    'status' => 'error',
                    'last_tested_at' => now(),
                    'last_error_message' => $errMsg,
                ]);

                return [
                    'success' => false,
                    'status_code' => $payload['code'] ?? $response->status(),
                    'latency_ms' => $latency,
                    'message' => 'Gagal terhubung ke FCS API: ' . $errMsg,
                    'error' => $errMsg,
                ];
            }

            // HTTP Error (4xx, 5xx)
            $errorMessage = "HTTP {$response->status()}: " . ($response->body() ?: 'Gagal menghubungi server FCS API');
            $config->update([
                'status' => 'error',
                'last_tested_at' => now(),
                'last_error_message' => $errorMessage,
            ]);

            return [
                'success' => false,
                'status_code' => $response->status(),
                'latency_ms' => $latency,
                'message' => 'Gagal terhubung ke FCS API: ' . $errorMessage,
                'error' => $response->body(),
            ];
        } catch (\Throwable $e) {
            $latency = (int) round((microtime(true) - $startTime) * 1000);
            Log::warning("FcsApi connection error: {$e->getMessage()}");

            $config->update([
                'status' => 'error',
                'last_tested_at' => now(),
                'last_error_message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status_code' => 500,
                'latency_ms' => $latency,
                'message' => 'Terjadi kesalahan koneksi ke FCS API: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Synchronize events from FCS API into economic_calendar_events table.
     */
    public function syncEvents(?string $apiKey = null, int $limit = 50): int
    {
        $config = $this->getConfig();
        $key = $apiKey ?: ($config->api_key ?: self::DEFAULT_API_KEY);
        $baseUrl = rtrim($config->base_url ?: self::DEFAULT_BASE_URL, '/');
        $endpoint = "{$baseUrl}/forex/economy_cal?access_key=" . urlencode($key);

        $response = Http::timeout(12)->get($endpoint);

        if (! $response->successful()) {
            return 0;
        }

        $payload = $response->json();
        if (! isset($payload['status']) || $payload['status'] !== true || empty($payload['response'])) {
            return 0;
        }

        $synced = 0;
        $items = array_slice($payload['response'], 0, $limit);

        foreach ($items as $item) {
            $country = self::mapCountryCode($item['country'] ?? 'US');
            $currency = ! empty($item['currency']) ? strtoupper(trim($item['currency'])) : 'USD';
            $eventName = ! empty($item['title']) ? trim($item['title']) : (! empty($item['indicator']) ? trim($item['indicator']) : 'Economic Event');

            // FCS importance: 3 => high, 2 => medium, 1/0 => low
            $importance = (string) ($item['importance'] ?? '0');
            $impactLevel = match ($importance) {
                '3' => 'high',
                '2' => 'medium',
                default => 'low',
            };

            $eventDate = ! empty($item['date']) ? Carbon::parse($item['date']) : now();

            EconomicCalendarEvent::updateOrCreate(
                [
                    'event_name' => $eventName,
                    'currency' => $currency,
                    'event_date' => $eventDate,
                ],
                [
                    'country' => $country,
                    'impact_level' => $impactLevel,
                    'actual' => ! empty($item['actual']) ? (string) $item['actual'] : null,
                    'forecast' => ! empty($item['forecast']) ? (string) $item['forecast'] : null,
                    'previous' => ! empty($item['previous']) ? (string) $item['previous'] : null,
                    'unit' => ! empty($item['unit']) ? (string) $item['unit'] : (! empty($item['scale']) ? (string) $item['scale'] : null),
                    'period' => ! empty($item['period']) ? (string) $item['period'] : null,
                    'source' => ! empty($item['source']) ? (string) $item['source'] : 'FCS API',
                ]
            );

            $synced++;
        }

        return $synced;
    }

    /**
     * Map country code to readable country name.
     */
    public static function mapCountryCode(string $code): string
    {
        return match (strtoupper(trim($code))) {
            'US' => 'United States',
            'GB', 'UK' => 'United Kingdom',
            'EU' => 'Euro Area',
            'DE' => 'Germany',
            'FR' => 'France',
            'IT' => 'Italy',
            'JP' => 'Japan',
            'CA' => 'Canada',
            'AU' => 'Australia',
            'NZ' => 'New Zealand',
            'CH' => 'Switzerland',
            'CN' => 'China',
            'BW' => 'Botswana',
            'PY' => 'Paraguay',
            'PA' => 'Panama',
            'IE' => 'Ireland',
            default => $code,
        };
    }
}
