<x-filament-panels::page>
    <x-module-placeholder
        title="Payment"
        group="SETTINGS"
        description="Konfigurasi kredensial Payment Gateway (Midtrans / Xendit), server key, dan webhook secret."
        icon="heroicon-o-banknotes"
        :features="array (
  0 => 'Pilihan gateway aktif (Midtrans / Xendit)',
  1 => 'Environment Mode (Sandbox / Production)',
  2 => 'Kredensial Server Key & Client Key',
  3 => 'Endpoint webhook URL & secret verification',
)"
        primaryAction="Kelola Data"
    />
</x-filament-panels::page>