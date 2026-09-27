<?php

namespace App\Filament\Pages\Settings;

use App\Domain\Settings\Models\LegalDocument;
use App\Domain\Settings\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LegalSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.settings.legal-settings-page';

    protected static string|BackedEnum|null $navigationIcon = 'bx-file';

    protected static \UnitEnum|string|null $navigationGroup = 'SETTINGS';

    protected static ?string $navigationLabel = 'Legal';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'legal';

    public ?array $data = [];

    public function getTitle(): string
    {
        return 'Pengaturan Dokumen Legalitas & Kebijakan Platform';
    }

    public function mount(): void
    {
        $settings = Setting::getGroup('legal');

        // Check if records exist in legal_documents table
        $termsDoc = LegalDocument::getCurrent('terms');
        $privacyDoc = LegalDocument::getCurrent('privacy');
        $refundDoc = LegalDocument::getCurrent('refund');

        $defaults = [
            'terms_version' => $termsDoc?->version ?? 'v1.2',
            'terms_title' => $termsDoc?->title ?? 'Syarat & Ketentuan Penggunaan Platform TradingEdu',
            'terms_effective_at' => $termsDoc?->effective_at?->format('Y-m-d H:i:s') ?? now()->startOfYear()->format('Y-m-d H:i:s'),
            'terms_content' => $termsDoc?->body ?? "1. Ketentuan Umum: Akses ke modul, analisa, dan ruang diskusi TradingEdu hanya untuk penggunaan perorangan berlisensi.\n2. Larangan Pembajakan: Dilarang keras merekam, menyebarluaskan, atau menjual materi kursus tanpa izin tertulis dari manajemen.\n3. Hak Akses Akun: Manajemen berhak menangguhkan akun yang terindikasi berbagi kredensial masuk kepada pihak ketiga.",

            'privacy_version' => $privacyDoc?->version ?? 'v1.1',
            'privacy_title' => $privacyDoc?->title ?? 'Kebijakan Perlindungan Data & Privasi Murid',
            'privacy_effective_at' => $privacyDoc?->effective_at?->format('Y-m-d H:i:s') ?? now()->startOfYear()->format('Y-m-d H:i:s'),
            'privacy_content' => $privacyDoc?->body ?? "1. Pengumpulan Data: Kami mengumpulkan nama, email, nomor kontak, serta log pembelajaran untuk keperluan sertifikasi dan akun pengguna.\n2. Kerahasiaan Informasi: Seluruh data pribadi dienkripsi sesuai standar UU Perlindungan Data Pribadi (UU PDP).\n3. Cookie & Analitik: Platform menggunakan cookie sesi terenkripsi untuk mengamankan login dan preferensi antarmuka pengguna.",

            'refund_version' => $refundDoc?->version ?? 'v1.0',
            'refund_title' => $refundDoc?->title ?? 'Kebijakan Pengembalian Dana & Jaminan Kepuasan',
            'refund_effective_at' => $refundDoc?->effective_at?->format('Y-m-d H:i:s') ?? now()->startOfYear()->format('Y-m-d H:i:s'),
            'refund_content' => $refundDoc?->body ?? "1. Garansi 7 Hari: Pembeli dapat mengajukan permohonan pengembalian dana penuh dalam kurun waktu 7 hari kalender sejak transaksi disetujui.\n2. Syarat & Ketentuan Refund: Pengembalian dana berlaku jika progres menonton video kursus belum melebihi 20% dan belum mengunduh materi instrumen indikator.\n3. Proses Pengembalian: Pengajuan yang disetujui akan diproses via transfer bank dalam 3-5 hari kerja.",
        ];

        $this->form->fill(array_merge($defaults, $settings));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Syarat & Ketentuan Layanan (Terms of Service)')
                    ->description('Perjanjian penggunaan platform antara murid dan pengelola TradingEdu.')
                    ->schema([
                        TextInput::make('terms_title')
                            ->label('Judul Dokumen')
                            ->required()
                            ->columnSpan(2),

                        TextInput::make('terms_version')
                            ->label('Versi Dokumen')
                            ->placeholder('v1.2')
                            ->required()
                            ->columnSpan(1),

                        DateTimePicker::make('terms_effective_at')
                            ->label('Tanggal Efektif Berlaku')
                            ->columnSpan(1),

                        Textarea::make('terms_content')
                            ->label('Isi Lengkap Dokumen Ketentuan')
                            ->rows(6)
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Kebijakan Privasi (Privacy Policy)')
                    ->description('Ketentuan perlindungan data pribadi murid sesuai regulasi PDP.')
                    ->schema([
                        TextInput::make('privacy_title')
                            ->label('Judul Dokumen')
                            ->required()
                            ->columnSpan(2),

                        TextInput::make('privacy_version')
                            ->label('Versi Dokumen')
                            ->placeholder('v1.1')
                            ->required()
                            ->columnSpan(1),

                        DateTimePicker::make('privacy_effective_at')
                            ->label('Tanggal Efektif Berlaku')
                            ->columnSpan(1),

                        Textarea::make('privacy_content')
                            ->label('Isi Lengkap Kebijakan Privasi')
                            ->rows(6)
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Kebijakan Garansi & Pengembalian Dana (Refund Policy)')
                    ->description('Ketentuan pengembalian dana investasi kelas bagi peserta.')
                    ->schema([
                        TextInput::make('refund_title')
                            ->label('Judul Dokumen')
                            ->required()
                            ->columnSpan(2),

                        TextInput::make('refund_version')
                            ->label('Versi Dokumen')
                            ->placeholder('v1.0')
                            ->required()
                            ->columnSpan(1),

                        DateTimePicker::make('refund_effective_at')
                            ->label('Tanggal Efektif Berlaku')
                            ->columnSpan(1),

                        Textarea::make('refund_content')
                            ->label('Isi Lengkap Kebijakan Garansi Refund')
                            ->rows(6)
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // 1. Simpan ke Setting key-value group
        Setting::setGroup('legal', $data);

        // 2. Sinkronkan dokumen ke tabel legal_documents
        $docs = [
            'terms' => [
                'title' => $data['terms_title'] ?? 'Syarat & Ketentuan Layanan',
                'version' => $data['terms_version'] ?? 'v1.0',
                'body' => $data['terms_content'] ?? '',
                'effective_at' => $data['terms_effective_at'] ?? now(),
            ],
            'privacy' => [
                'title' => $data['privacy_title'] ?? 'Kebijakan Privasi',
                'version' => $data['privacy_version'] ?? 'v1.0',
                'body' => $data['privacy_content'] ?? '',
                'effective_at' => $data['privacy_effective_at'] ?? now(),
            ],
            'refund' => [
                'title' => $data['refund_title'] ?? 'Kebijakan Refund',
                'version' => $data['refund_version'] ?? 'v1.0',
                'body' => $data['refund_content'] ?? '',
                'effective_at' => $data['refund_effective_at'] ?? now(),
            ],
        ];

        foreach ($docs as $type => $docData) {
            LegalDocument::where('type', $type)->update(['is_current' => false]);

            LegalDocument::updateOrCreate(
                ['type' => $type, 'version' => $docData['version']],
                [
                    'title' => $docData['title'],
                    'body' => $docData['body'],
                    'effective_at' => $docData['effective_at'],
                    'is_current' => true,
                ]
            );
        }

        Notification::make()
            ->title('Dokumen Legalitas Berhasil Disimpan')
            ->body('Dokumen Terms of Service, Privacy Policy, dan Refund Policy telah diperbarui.')
            ->success()
            ->send();
    }
}
