<?php

namespace Database\Seeders;

use App\Domain\Settings\Models\LegalDocument;
use App\Domain\Settings\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. General Settings
        Setting::setGroup('general', [
            'site_name' => 'TradingEdu - Platform Edukasi Trading Profesional',
            'site_tagline' => 'Kuasai Analisa Pasar Forex, Emas, dan Kripto dengan Presisi Institusional',
            'support_email' => 'support@tradingedu.com',
            'support_whatsapp' => '+6281234567890',
            'site_logo_url' => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=100&auto=format&fit=crop',
            'site_favicon_url' => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=32&auto=format&fit=crop',
            'default_timezone' => 'Asia/Jakarta',
            'default_currency' => 'IDR',
            'default_locale' => 'id',
            'maintenance_mode' => false,
            'allow_user_registration' => true,
            'maintenance_message' => 'TradingEdu sedang dalam proses pemeliharaan rutin. Kami akan segera kembali online.',
        ]);

        // 2. Payment Settings
        Setting::setGroup('payment', [
            'midtrans_enabled' => true,
            'midtrans_environment' => 'sandbox',
            'midtrans_merchant_id' => 'G123456789',
            'midtrans_client_key' => 'SB-Mid-client-demo12345678',
            'midtrans_server_key' => 'SB-Mid-server-demo98765432',
            'midtrans_enable_3ds' => true,

            'xendit_enabled' => false,
            'xendit_environment' => 'sandbox',
            'xendit_public_key' => 'xnd_public_development_abc123',
            'xendit_secret_key' => 'xnd_development_secretkeyxyz987',
            'xendit_webhook_token' => 'xnd_token_verify_randomstring',

            'manual_transfer_enabled' => true,
            'bank_name' => 'Bank Central Asia (BCA)',
            'bank_account_number' => '8012345678',
            'bank_account_holder' => 'PT TRADING EDU INDONESIA',
            'transfer_instructions' => "1. Transfer tepat sesuai nominal hingga 3 digit terakhir.\n2. Cantumkan ID Pesanan pada berita transfer.\n3. Unggah bukti pembayaran melalui dashboard profil murid.",

            'order_expiry_hours' => 24,
            'auto_cancel_expired' => true,
            'unique_code_enabled' => true,
        ]);

        // 3. Email Settings
        Setting::setGroup('email', [
            'mail_mailer' => 'smtp',
            'mail_host' => 'smtp.mailgun.org',
            'mail_port' => '587',
            'mail_encryption' => 'tls',
            'mail_username' => 'postmaster@sandbox.tradingedu.com',
            'mail_password' => 'secret_smtp_password_1234',

            'mail_from_name' => 'TradingEdu Official',
            'mail_from_address' => 'support@tradingedu.com',
            'mail_reply_to_address' => 'help@tradingedu.com',

            'notify_welcome_email' => true,
            'notify_order_invoice' => true,
            'notify_order_success' => true,
            'notify_live_reminder' => true,
            'notify_admin_new_order' => false,
        ]);

        // 4. Legal Documents & Settings
        $termsContent = "1. Ketentuan Umum: Akses ke seluruh modul, analisa teknikal, dan komunitas TradingEdu bersifat lisensi individual dan tidak dapat dialihkan.\n2. Hak Kekayaan Intelektual: Seluruh materi grafis, indikator kustom, dan video tutorial dilindungi hak cipta.\n3. Kode Etik Diskusi: Dilarang melakukan spamming, promosi skema cepat kaya, atau penipuan investasi di komunitas.";
        $privacyContent = "1. Pengumpulan Data: Informasi profil, progres kursus, dan rekam jejak kuis disimpan dengan enkripsi untuk keperluan sertifikasi.\n2. Perlindungan Privasi: Data tidak akan diperjualbelikan kepada pihak ketiga manapun.\n3. Hak Pengguna: Murid berhak meminta penghapusan riwayat akun dan data personal kapan saja.";
        $refundContent = "1. Jaminan Uang Kembali 7 Hari: Pengembalian dana penuh tersedia apabila progres kursus di bawah 20% dan belum mengunduh indikator eksklusif.\n2. Verifikasi Pengajuan: Permintaan refund akan diproses dalam 3-5 hari kerja setelah ditinjau oleh tim kepatuhan kami.";

        Setting::setGroup('legal', [
            'terms_version' => 'v1.2',
            'terms_title' => 'Syarat & Ketentuan Penggunaan Platform TradingEdu',
            'terms_effective_at' => now()->startOfYear()->toDateTimeString(),
            'terms_content' => $termsContent,

            'privacy_version' => 'v1.1',
            'privacy_title' => 'Kebijakan Perlindungan Data & Privasi Murid',
            'privacy_effective_at' => now()->startOfYear()->toDateTimeString(),
            'privacy_content' => $privacyContent,

            'refund_version' => 'v1.0',
            'refund_title' => 'Kebijakan Pengembalian Dana & Jaminan Kepuasan',
            'refund_effective_at' => now()->startOfYear()->toDateTimeString(),
            'refund_content' => $refundContent,
        ]);

        LegalDocument::updateOrCreate(
            ['type' => 'terms', 'version' => 'v1.2'],
            [
                'title' => 'Syarat & Ketentuan Penggunaan Platform TradingEdu',
                'body' => $termsContent,
                'effective_at' => now()->startOfYear(),
                'is_current' => true,
            ]
        );

        LegalDocument::updateOrCreate(
            ['type' => 'privacy', 'version' => 'v1.1'],
            [
                'title' => 'Kebijakan Perlindungan Data & Privasi Murid',
                'body' => $privacyContent,
                'effective_at' => now()->startOfYear(),
                'is_current' => true,
            ]
        );

        LegalDocument::updateOrCreate(
            ['type' => 'refund', 'version' => 'v1.0'],
            [
                'title' => 'Kebijakan Pengembalian Dana & Jaminan Kepuasan',
                'body' => $refundContent,
                'effective_at' => now()->startOfYear(),
                'is_current' => true,
            ]
        );

        // 5. Disclaimer Settings & Document
        $disclaimerBody = "1. Karakteristik Pasar Berisiko Tinggi: Perdagangan valuta asing (Forex), komoditas berjangka, indeks saham, dan aset kripto melibatkan risiko finansial yang sangat signifikan dan berpotensi menyebabkan kehilangan seluruh modal investasi Anda.\n2. Tingkat Leverage: Penggunaan leverage tinggi dapat melipatgandakan potensi kerugian sebagaimana ia melipatgandakan potensi keuntungan.\n3. Pertimbangan Profil Investor: Sebelum memutuskan untuk bertrading, Anda harus mempertimbangkan dengan cermat tujuan investasi, tingkat pengalaman, serta toleransi terhadap risiko Anda.";

        Setting::setGroup('disclaimer', [
            'disclaimer_version' => 'v1.0',
            'disclaimer_title' => 'Peringatan Risiko Pasar Finansial & Penafian Tanggung Jawab',
            'disclaimer_body' => $disclaimerBody,
            'not_financial_advice_text' => 'Seluruh materi edukasi, analisa pasar, dan diskusi di platform TradingEdu murni untuk tujuan literasi finansial dan bukan merupakan anjuran, rekomendasi investasi, atau ajakan membeli/menjual instrumen keuangan apa pun.',
            'past_performance_warning' => 'Kinerja historis atau keuntungan di masa lampau BUKAN merupakan jaminan atas hasil di masa mendatang. Risiko kerugian sepenuhnya ditanggung oleh masing-masing pelaku pasar.',
            'show_modal_on_first_visit' => true,
            'require_checkbox_on_checkout' => true,
            'show_footer_disclaimer' => true,
            'footer_disclaimer_summary' => 'Peringatan Risiko: Perdagangan instrumen finansial dengan leverage membawa tingkat risiko modal yang tinggi. Harap pahami profil risiko Anda sebelum bertransaksi.',
        ]);

        LegalDocument::updateOrCreate(
            ['type' => 'disclaimer', 'version' => 'v1.0'],
            [
                'title' => 'Peringatan Risiko Pasar Finansial & Penafian Tanggung Jawab',
                'body' => $disclaimerBody,
                'effective_at' => now()->startOfYear(),
                'is_current' => true,
            ]
        );
    }
}
