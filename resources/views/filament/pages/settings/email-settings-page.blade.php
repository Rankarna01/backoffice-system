<x-filament-panels::page>
    <x-module-placeholder
        title="Email"
        group="SETTINGS"
        description="Pengaturan pengiriman email transaksional SMTP, invoice bukti bayar, dan verifikasi akun."
        icon="heroicon-o-envelope"
        :features="array (
  0 => 'Kredensial server SMTP / Resend / Mailgun',
  1 => 'Nama & alamat email pengirim resmi',
  2 => 'Uji coba kirim email test',
  3 => 'Template email invoice pembayaran',
)"
        primaryAction="Kelola Data"
    />
</x-filament-panels::page>