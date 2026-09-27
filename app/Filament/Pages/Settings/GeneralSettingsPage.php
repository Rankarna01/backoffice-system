<?php

namespace App\Filament\Pages\Settings;

use App\Domain\Settings\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GeneralSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.settings.general-settings-page';

    protected static string|BackedEnum|null $navigationIcon = 'bx-cog';

    protected static \UnitEnum|string|null $navigationGroup = 'SETTINGS';

    protected static ?string $navigationLabel = 'General';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'general';

    public ?array $data = [];

    public function getTitle(): string
    {
        return 'Pengaturan Umum';
    }

    public function mount(): void
    {
        $settings = Setting::getGroup('general');

        $defaults = [
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
        ];

        $this->form->fill(array_merge($defaults, $settings));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Identitas & Informasi Brand Platform')
                    ->description('Konfigurasi nama situs, kontak bantuan, dan logo portal.')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Nama Platform / Situs')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        TextInput::make('site_tagline')
                            ->label('Slogan / Tagline')
                            ->maxLength(255)
                            ->columnSpan(1),

                        TextInput::make('support_email')
                            ->label('Email Layanan Pelanggan')
                            ->email()
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('support_whatsapp')
                            ->label('Nomor WhatsApp Support')
                            ->placeholder('+6281234567890')
                            ->columnSpan(1),

                        TextInput::make('site_logo_url')
                            ->label('URL Logo Utama')
                            ->placeholder('https://... url logo')
                            ->columnSpan(1),

                        TextInput::make('site_favicon_url')
                            ->label('URL Favicon')
                            ->placeholder('https://... url favicon')
                            ->columnSpan(1),
                    ])->columns(2),

                Section::make('Regional & Lokalisasi Sistem')
                    ->description('Format mata uang acuan, zona waktu pasar, dan bahasa default.')
                    ->schema([
                        Select::make('default_timezone')
                            ->label('Zona Waktu Acuan')
                            ->options([
                                'Asia/Jakarta' => 'WIB - Waktu Indonesia Barat (Asia/Jakarta)',
                                'Asia/Makassar' => 'WITA - Waktu Indonesia Tengah (Asia/Makassar)',
                                'Asia/Jayapura' => 'WIT - Waktu Indonesia Timur (Asia/Jayapura)',
                                'UTC' => 'UTC / GMT (Universal Time)',
                            ])
                            ->default('Asia/Jakarta')
                            ->required()
                            ->columnSpan(1),

                        Select::make('default_currency')
                            ->label('Mata Uang Transaksi Default')
                            ->options([
                                'IDR' => 'IDR - Rupiah Indonesia (Rp)',
                                'USD' => 'USD - United States Dollar ($)',
                            ])
                            ->default('IDR')
                            ->required()
                            ->columnSpan(1),

                        Select::make('default_locale')
                            ->label('Bahasa Tampilan Antarmuka')
                            ->options([
                                'id' => 'Bahasa Indonesia',
                                'en' => 'English (US)',
                            ])
                            ->default('id')
                            ->required()
                            ->columnSpan(1),
                    ])->columns(2),

                Section::make('Kontrol Operasional & Mode Pemeliharaan')
                    ->description('Pengaturan akses registrasi murid dan mode maintenance darurat.')
                    ->schema([
                        Toggle::make('allow_user_registration')
                            ->label('Izinkan Registrasi Akun Baru')
                            ->default(true)
                            ->helperText('Jika dinonaktifkan, tombol pendaftaran baru akan ditutup sementara.')
                            ->columnSpan(1),

                        Toggle::make('maintenance_mode')
                            ->label('Mode Pemeliharaan (Maintenance Mode)')
                            ->default(false)
                            ->helperText('Hanya administrator yang dapat mengakses saat mode ini aktif.')
                            ->columnSpan(1),

                        Textarea::make('maintenance_message')
                            ->label('Pesan Pemeliharaan Layar Depan')
                            ->placeholder('Tuliskan estimasi selesai pemeliharaan...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        Setting::setGroup('general', $data);

        Notification::make()
            ->title('Pengaturan Umum Berhasil Disimpan')
            ->body('Seluruh konfigurasi identitas dan regional platform telah diperbarui.')
            ->success()
            ->send();
    }
}
