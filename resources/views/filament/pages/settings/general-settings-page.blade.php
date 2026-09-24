<x-filament-panels::page>
    <x-module-placeholder
        title="General"
        group="SETTINGS"
        description="Pengaturan umum sistem: nama platform, kontak resmi WhatsApp/Telegram, dan timezone default."
        icon="heroicon-o-adjustments-horizontal"
        :features="array (
  0 => 'Nama platform & deskripsi resmi',
  1 => 'Kontak layanan pelanggan & support',
  2 => 'Format mata uang & zona waktu default',
  3 => 'Mode pemeliharaan (Maintenance mode)',
)"
        primaryAction="Kelola Data"
    />
</x-filament-panels::page>