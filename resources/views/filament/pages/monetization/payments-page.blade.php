<x-filament-panels::page>
    <x-module-placeholder
        title="Payments"
        group="MONETIZATION"
        description="Pelacakan rekonsiliasi pembayaran gateway (QRIS, VA Bank, E-Wallet, Kartu Kredit)."
        icon="heroicon-o-credit-card"
        :features="array (
  0 => 'Webhook log & signature verification',
  1 => 'Pencatatan ID transaksi gateway eksternal',
  2 => 'Status pembayaran real-time',
  3 => 'Fasilitas refund manual oleh admin',
)"
        primaryAction="Kelola Data"
    />
</x-filament-panels::page>