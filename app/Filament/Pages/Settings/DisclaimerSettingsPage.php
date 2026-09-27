<?php

namespace App\Filament\Pages\Settings;

use App\Domain\Settings\Models\LegalDocument;
use App\Domain\Settings\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DisclaimerSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.settings.disclaimer-settings-page';

    protected static string|BackedEnum|null $navigationIcon = 'bx-shield-quarter';

    protected static \UnitEnum|string|null $navigationGroup = 'SETTINGS';

    protected static ?string $navigationLabel = 'Disclaimer';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'disclaimer';

    public ?array $data = [];

    public function getTitle(): string
    {
        return 'Pengaturan Peringatan Risiko & Disclaimer Finansial';
    }

    public function mount(): void
    {
        $settings = Setting::getGroup('disclaimer');

        $doc = LegalDocument::getCurrent('disclaimer');

        $defaults = [
            'disclaimer_version' => $doc?->version ?? 'v1.0',
            'disclaimer_title' => $doc?->title ?? 'Peringatan Risiko Pasar Finansial & Penafian Tanggung Jawab',
            'disclaimer_body' => $doc?->body ?? "1. Karakteristik Pasar Berisiko Tinggi: Perdagangan valuta asing (Forex), komoditas berjangka, indeks saham, dan aset kripto melibatkan risiko finansial yang sangat signifikan dan berpotensi menyebabkan kehilangan seluruh modal investasi Anda.\n2. Tingkat Leverage: Penggunaan leverage tinggi dapat melipatgandakan potensi kerugian sebagaimana ia melipatgandakan potensi keuntungan.\n3. Pertimbangan Profil Investor: Sebelum memutuskan untuk bertrading, Anda harus mempertimbangkan dengan cermat tujuan investasi, tingkat pengalaman, serta toleransi terhadap risiko Anda.",

            'not_financial_advice_text' => 'Seluruh informasi, kurikulum, sesi siaran langsung, sinyal teknikal, dan artikel outlook yang disajikan di TradingEdu murni untuk tujuan edukasi dan pelatihan analisis pasar. Tidak ada materi yang boleh ditafsirkan sebagai rekomendasi, nasihat keuangan, ataupun ajakan untuk membeli atau menjual instrumen keuangan apa pun.',
            'past_performance_warning' => 'Kinerja historis atau rekam jejak analisa di masa lampau BUKAN merupakan jaminan atas hasil di masa depan. Hasil trading setiap individu dapat bervariasi bergantung pada eksekusi dan manajemen risiko masing-masing.',

            'show_modal_on_first_visit' => true,
            'require_checkbox_on_checkout' => true,
            'show_footer_disclaimer' => true,
            'footer_disclaimer_summary' => 'Peringatan Risiko: Trading instrumen finansial dengan leverage membawa risiko modal yang tinggi. Pastikan Anda memahami sepenuhnya risiko yang terlibat sebelum melakukan transaksi riil. TradingEdu adalah penyedia konten edukasi finansial independen dan bukan entitas pialang/broker berjangka.',
        ];

        $this->form->fill(array_merge($defaults, $settings));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Dokumen Peringatan Risiko Utama (High-Risk Investment Warning)')
                    ->description('Pernyataan resmi keterbukaan risiko pasar bagi seluruh pengunjung dan member platform.')
                    ->schema([
                        TextInput::make('disclaimer_title')
                            ->label('Judul Dokumen Disclaimer')
                            ->required()
                            ->columnSpan(2),

                        TextInput::make('disclaimer_version')
                            ->label('Versi Dokumen')
                            ->placeholder('v1.0')
                            ->required()
                            ->columnSpan(1),

                        Textarea::make('disclaimer_body')
                            ->label('Isi Lengkap Dokumen Peringatan Risiko')
                            ->rows(6)
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Penafian Nasihat Keuangan (No Financial Advice Disclosure)')
                    ->description('Klausul proteksi hukum yang menegaskan platform murni berorientasi edukasi.')
                    ->schema([
                        Textarea::make('not_financial_advice_text')
                            ->label('Pernyataan Bukan Nasihat Finansial')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('past_performance_warning')
                            ->label('Peringatan Rekam Jejak Historis (Past Performance)')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Visibilitas & Penegakan di Frontend Platform')
                    ->description('Konfigurasi pop-up persetujuan risiko, syarat checkout, dan footer disclaimer.')
                    ->schema([
                        Toggle::make('show_modal_on_first_visit')
                            ->label('Tampilkan Pop-up Konfirmasi Risiko Saat Pertama Kali Kunjungan')
                            ->default(true)
                            ->helperText('Pengunjung wajib menyetujui peringatan risiko sebelum menavigasi lebih jauh.')
                            ->columnSpan(1),

                        Toggle::make('require_checkbox_on_checkout')
                            ->label('Wajib Centang Persetujuan Risiko Saat Pembayaran (Checkout)')
                            ->default(true)
                            ->helperText('Murid wajib mencentang checkbox pemahaman risiko sebelum dapat membayar pesanan.')
                            ->columnSpan(1),

                        Toggle::make('show_footer_disclaimer')
                            ->label('Tampilkan Disclaimer Ringkas di Footer Website')
                            ->default(true)
                            ->columnSpanFull(),

                        Textarea::make('footer_disclaimer_summary')
                            ->label('Teks Ringkasan Disclaimer Footer')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // 1. Simpan ke Setting key-value group
        Setting::setGroup('disclaimer', $data);

        // 2. Sinkronkan ke tabel legal_documents
        LegalDocument::where('type', 'disclaimer')->update(['is_current' => false]);

        LegalDocument::updateOrCreate(
            ['type' => 'disclaimer', 'version' => $data['disclaimer_version'] ?? 'v1.0'],
            [
                'title' => $data['disclaimer_title'] ?? 'Peringatan Risiko Pasar Finansial',
                'body' => $data['disclaimer_body'] ?? '',
                'effective_at' => now(),
                'is_current' => true,
            ]
        );

        Notification::make()
            ->title('Pengaturan Disclaimer Berhasil Disimpan')
            ->body('Ketentuan peringatan risiko pasar dan aturan penayangan frontend telah diperbarui.')
            ->success()
            ->send();
    }
}
