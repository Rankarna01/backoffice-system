<x-filament-panels::page>
    <x-module-placeholder
        title="Roles & Permissions"
        group="USER MANAGEMENT"
        description="Konfigurasi hak akses sistem: Super Admin, Admin Operasional, Mentor, dan Customer."
        icon="heroicon-o-shield-check"
        :features="array (
  0 => 'Role hierarchy & permissions Spatie',
  1 => 'Matriks hak akses fitur & menu',
  2 => 'Audit log hak akses pengguna',
  3 => 'Guard web & api token privileges',
)"
        primaryAction="Kelola Data"
    />
</x-filament-panels::page>