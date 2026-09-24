<x-filament-panels::page>
    <x-module-placeholder
        title="Economic Calendar"
        group="MARKET"
        description="Jadwal rilis data ekonomi dunia (NFP, CPI, Suku Bunga) beserta tingkat impak (High/Medium/Low)."
        icon="heroicon-o-calendar-days"
        :features="array (
  0 => 'Klasifikasi data berdampak tinggi (High Impact)',
  1 => 'Angka Aktual, Prediksi (Forecast), dan Sebelumnya',
  2 => 'Penyesuaian zona waktu lokal (WIB)',
  3 => 'Filter instrumen terdampak (USD, EUR, GBP, JPY)',
)"
        primaryAction="Kelola Data"
    />
</x-filament-panels::page>