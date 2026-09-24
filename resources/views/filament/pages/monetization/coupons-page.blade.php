<x-filament-panels::page>
    <x-module-placeholder
        title="Coupons"
        group="MONETIZATION"
        description="Pembuatan kode promo kupon diskon (persentase atau nominal rupiah) dengan kuota pemakaian."
        icon="heroicon-o-ticket"
        :features="array (
  0 => 'Kode kupon & tipe diskon (Fixed IDR / %)',
  1 => 'Periode berlaku & batas kuota total',
  2 => 'Batas pemakaian per user',
  3 => 'Syarat minimum belanja & cakupan kursus',
)"
        primaryAction="Kelola Data"
    />
</x-filament-panels::page>