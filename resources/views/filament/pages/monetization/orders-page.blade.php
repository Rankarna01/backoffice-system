<x-filament-panels::page>
    <x-module-placeholder
        title="Orders"
        group="MONETIZATION"
        description="Daftar semua transaksi pembelian kursus dan paket langganan dari checkout customer."
        icon="heroicon-o-shopping-cart"
        :features="array (
  0 => 'Nomor invoice unik & snapshot rincian order',
  1 => 'Status: Pending, Paid, Expired, Refunded',
  2 => 'Penerapan diskon kode kupon',
  3 => 'Informasi referral affiliate pembeli',
)"
        primaryAction="Kelola Data"
    />
</x-filament-panels::page>