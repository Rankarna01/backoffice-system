<x-filament-panels::page>
    <x-module-placeholder
        title="Reviews"
        group="CONTENT"
        description="Ulasan rating bintang (1-5) dan review dari murid yang telah menyelesaikan kursus."
        icon="heroicon-o-star"
        :features="array (
  0 => 'Rating bintang 1-5 & komentar murid',
  1 => 'Moderasi status: Pending, Approved, Hidden',
  2 => 'Pencegahan fake review (hanya verified buyers)',
  3 => 'Kalkulasi rata-rata rating kursus',
)"
        primaryAction="Kelola Data"
    />
</x-filament-panels::page>