<x-filament-panels::page>
    <x-module-placeholder
        title="Legal"
        group="SETTINGS"
        description="Dokumen hukum resmi platform: Syarat & Ketentuan (Terms of Service) dan Kebijakan Privasi (Privacy Policy)."
        icon="heroicon-o-document-text"
        :features="array (
  0 => 'Editor naskah Terms of Service bertipe rich text',
  1 => 'Editor naskah Kebijakan Privasi (Privacy Policy)',
  2 => 'Pencatatan nomor versi dokumen hukum',
  3 => 'Penegakan persetujuan saat customer mendaftar',
)"
        primaryAction="Kelola Data"
    />
</x-filament-panels::page>