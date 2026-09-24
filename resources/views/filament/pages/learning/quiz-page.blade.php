<x-filament-panels::page>
    <x-module-placeholder
        title="Quiz"
        group="LEARNING"
        description="Evaluasi pemahaman member melalui kuis pilihan ganda, benar/salah, dan pembahasan soal."
        icon="heroicon-o-question-mark-circle"
        :features="array (
  0 => 'Bank soal kuis & penjelasan kunci jawaban',
  1 => 'Passing grade & batas pengulangan kuis',
  2 => 'Acak soal & opsi jawaban',
  3 => 'Riwayat percobaan & nilai murid',
)"
        primaryAction="Kelola Data"
    />
</x-filament-panels::page>