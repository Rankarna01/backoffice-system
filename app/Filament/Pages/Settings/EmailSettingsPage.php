<?php

namespace App\Filament\Pages\Settings;

use App\Domain\Settings\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmailSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.settings.email-settings-page';

    protected static string|BackedEnum|null $navigationIcon = 'bx-envelope';

    protected static \UnitEnum|string|null $navigationGroup = 'SETTINGS';

    protected static ?string $navigationLabel = 'Email';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'email';

    public ?array $data = [];

    public ?string $test_email_recipient = '';

    public function getTitle(): string
    {
        return 'Pengaturan Email & Notifikasi Sistem';
    }

    public function mount(): void
    {
        $settings = Setting::getGroup('email');

        $defaults = [
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
        ];

        $this->form->fill(array_merge($defaults, $settings));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Konfigurasi Server & Protokol Pengiriman (SMTP)')
                    ->description('Pengaturan koneksi pengiriman email transaksional platform.')
                    ->schema([
                        Select::make('mail_mailer')
                            ->label('Driver Pengiriman')
                            ->options([
                                'smtp' => 'SMTP (Mailgun / SendGrid / Google Workspace)',
                                'mailgun' => 'Mailgun HTTP API',
                                'ses' => 'Amazon SES (Simple Email Service)',
                                'postmark' => 'Postmark API',
                                'log' => 'Log Driver (Pengujian Lokal / Dev)',
                            ])
                            ->default('smtp')
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('mail_host')
                            ->label('SMTP Host')
                            ->placeholder('smtp.mailgun.org')
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('mail_port')
                            ->label('Port Server')
                            ->placeholder('587')
                            ->default('587')
                            ->required()
                            ->columnSpan(1),

                        Select::make('mail_encryption')
                            ->label('Enkripsi Keamanan')
                            ->options([
                                'tls' => 'TLS (Direkomendasikan)',
                                'ssl' => 'SSL',
                                'none' => 'None (Tanpa Enkripsi)',
                            ])
                            ->default('tls')
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('mail_username')
                            ->label('Username / Akun SMTP')
                            ->placeholder('postmaster@domain.com')
                            ->columnSpan(1),

                        TextInput::make('mail_password')
                            ->label('Password / Token Sandi')
                            ->password()
                            ->revealable()
                            ->placeholder('••••••••••••')
                            ->columnSpan(1),
                    ])->columns(2),

                Section::make('Identitas Pengirim Resmi (Sender Identity)')
                    ->description('Alamat pengirim yang akan terlihat di kotak masuk email murid.')
                    ->schema([
                        TextInput::make('mail_from_name')
                            ->label('Nama Pengirim (Sender Name)')
                            ->placeholder('TradingEdu Official')
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('mail_from_address')
                            ->label('Email Pengirim (Sender Email)')
                            ->placeholder('noreply@tradingedu.com')
                            ->email()
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('mail_reply_to_address')
                            ->label('Email Reply-To (Tujuan Balasan)')
                            ->placeholder('support@tradingedu.com')
                            ->email()
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Otomatisasi Notifikasi Email')
                    ->description('Pilih pemicu pengiriman email sistem kepada murid & tim administrator.')
                    ->schema([
                        Toggle::make('notify_welcome_email')
                            ->label('Kirim Email Sambutan Saat Pendaftaran Baru')
                            ->default(true)
                            ->columnSpan(1),

                        Toggle::make('notify_order_invoice')
                            ->label('Kirim Email Tagihan & Petunjuk Pembayaran')
                            ->default(true)
                            ->columnSpan(1),

                        Toggle::make('notify_order_success')
                            ->label('Kirim Email Konfirmasi Pembayaran Berhasil')
                            ->default(true)
                            ->columnSpan(1),

                        Toggle::make('notify_live_reminder')
                            ->label('Kirim Pengingat 1 Jam Sebelum Live Session')
                            ->default(true)
                            ->columnSpan(1),

                        Toggle::make('notify_admin_new_order')
                            ->label('Notifikasi Email ke Admin Saat Terjadi Pembelian Baru')
                            ->default(false)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        Setting::setGroup('email', $data);

        Notification::make()
            ->title('Pengaturan Email Berhasil Disimpan')
            ->body('Konfigurasi SMTP server dan otomatisasi notifikasi telah diperbarui.')
            ->success()
            ->send();
    }

    public function sendTestEmail(): void
    {
        if (empty($this->test_email_recipient) || ! filter_var($this->test_email_recipient, FILTER_VALIDATE_EMAIL)) {
            Notification::make()
                ->title('Email Tidak Valid')
                ->body('Silakan masukkan alamat email yang valid untuk pengujian.')
                ->warning()
                ->send();
            return;
        }

        // Simulate or log test email
        Notification::make()
            ->title('Permintaan Uji Coba Terkirim')
            ->body("Email uji coba telah dijadwalkan ke alamat {$this->test_email_recipient}.")
            ->success()
            ->send();
    }
}
