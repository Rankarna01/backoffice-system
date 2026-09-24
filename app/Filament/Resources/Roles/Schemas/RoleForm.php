<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class RoleForm
{
    /**
     * Map permission names to human-readable Indonesian labels
     */
    protected static array $permissionLabels = [
        // General / Overview
        'view-dashboard' => 'Dashboard: Akses Tampilan Ringkasan',
        'manage-users' => 'Users: Kelola Seluruh Pengguna',
        'manage-courses' => 'Courses: Kelola Semua Kursus Edukasi',
        'manage-signals' => 'Signals: Kelola Semua Sinyal Trading',
        'manage-orders' => 'Orders: Kelola Transaksi & Pesanan',
        'manage-community' => 'Community: Kelola Forum & Sesi Live',
        'manage-settings' => 'Settings: Akses Pengaturan Global',
        'access-learning' => 'Learning: Akses Materi Belajar Kursus (Member)',
        'access-trading-tools' => 'Tools: Akses Kalkulator & Alat Bantu Trading',
        'access-market-signals' => 'Signals: Akses Sinyal Pasar Real-time',

        // User Management
        'users.view' => 'Users: Lihat Daftar Pengguna',
        'users.create' => 'Users: Tambah Pengguna Baru',
        'users.update' => 'Users: Ubah Data Pengguna',
        'users.delete' => 'Users: Hapus / Nonaktifkan Pengguna',
        'mentors.manage' => 'Mentors: Kelola Profil & Mentor Unggulan',
        'roles.manage' => 'Roles: Kelola Hak Akses & Peran Pengguna',

        // Learning & Courses
        'courses.view' => 'Courses: Lihat Katalog Kursus',
        'courses.create' => 'Courses: Buat Kursus Baru',
        'courses.update' => 'Courses: Edit Kurikulum & Materi',
        'courses.delete' => 'Courses: Hapus Kursus',
        'courses.publish' => 'Courses: Publikasikan / Arsipkan Kursus',
        'modules.manage' => 'Modules: Kelola Bab & Modul Pembelajaran',
        'lessons.manage' => 'Lessons: Kelola Lesson, Video & Konten',
        'quizzes.manage' => 'Quizzes: Kelola Kuis & Evaluasi Belajar',
        'certificates.manage' => 'Certificates: Atur & Terbitkan Sertifikat',

        // Market & Signals
        'signals.view' => 'Signals: Lihat Daftar Sinyal',
        'signals.create' => 'Signals: Buat Sinyal Trading Baru',
        'signals.update' => 'Signals: Ubah Status & Target TP/SL Sinyal',
        'signals.delete' => 'Signals: Batalkan / Hapus Sinyal',
        'signals.publish' => 'Signals: Broadcast Sinyal ke Member',
        'market_outlooks.manage' => 'Outlook: Kelola Analisa Pasar & Macro',
        'news.manage' => 'News: Kelola Berita Trading Forex/Crypto',
        'economic_calendar.manage' => 'Calendar: Kelola Agenda Kalender Ekonomi',

        // Community & Live Sessions
        'discussions.manage' => 'Discussions: Moderasi Forum & Komentar Komunitas',
        'live_sessions.manage' => 'Live: Jadwalkan & Kelola Live Trading / Webinar',

        // Monetization & Sales
        'orders.view' => 'Orders: Lihat Riwayat Transaksi Pesanan',
        'orders.update' => 'Orders: Ubah Status Pesanan Manual',
        'orders.refund' => 'Orders: Otorisasi Pengembalian Dana (Refund)',
        'subscriptions.manage' => 'Subscriptions: Kelola Paket Langganan Pro/VIP',
        'coupons.manage' => 'Coupons: Kelola Kupon Diskon Promo',
        'affiliates.manage' => 'Affiliates: Kelola Komisi & Jaringan Afiliasi',

        // Content & Website
        'announcements.manage' => 'Announcements: Kirim Pengumuman Siaran',
        'reviews.manage' => 'Reviews: Moderasi Rating & Ulasan Kursus',
        'testimonials.manage' => 'Testimonials: Kelola Testimoni Member',
        'faqs.manage' => 'FAQ: Kelola Tanya Jawab Umum',
        'landing_page.manage' => 'Website: Kelola Banner & Halaman Utama',

        // System & Settings
        'settings.view' => 'Settings: Lihat Konfigurasi Sistem',
        'settings.update' => 'Settings: Ubah Konfigurasi Sistem & Payment Gateway',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Peran (Role)')
                    ->description('Tentukan nama peran sistem dan guard autentikasi.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Role')
                                    ->placeholder('Contoh: financial_analyst, community_moderator')
                                    ->required()
                                    ->unique(Role::class, 'name', ignoreRecord: true)
                                    ->maxLength(255)
                                    ->disabled(fn (?Role $record): bool => in_array($record?->name, ['super_admin']))
                                    ->helperText(fn (?Role $record): string => in_array($record?->name, ['super_admin'])
                                        ? 'Peran super_admin adalah peran inti sistem dan tidak dapat diubah namanya.'
                                        : 'Gunakan format huruf kecil atau snake_case.'),

                                TextInput::make('guard_name')
                                    ->label('Guard Otentikasi')
                                    ->default('web')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('Default guard untuk autentikasi web.'),
                            ]),
                    ]),

                Section::make('Matriks Hak Akses (Permissions)')
                    ->description('Centang izin fitur yang diberikan kepada peran ini. Gunakan fitur pencarian untuk menemukan izin dengan cepat.')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->label('Daftar Izin Fitur')
                            ->relationship('permissions', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => self::$permissionLabels[$record->name] ?? $record->name)
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(2)
                            ->gridDirection('row')
                            ->helperText('Gunakan tombol Select All / Deselect All di atas untuk mempermudah pemilihan hak akses massal.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
