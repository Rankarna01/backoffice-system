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

class PaymentSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.settings.payment-settings-page';

    protected static string|BackedEnum|null $navigationIcon = 'bx-credit-card';

    protected static \UnitEnum|string|null $navigationGroup = 'SETTINGS';

    protected static ?string $navigationLabel = 'Payment';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'payment';

    public ?array $data = [];

    public function getTitle(): string
    {
        return 'Pengaturan Pembayaran & Gateway';
    }

    public function mount(): void
    {
        $settings = Setting::getGroup('payment');

        $defaults = [
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
        ];

        $this->form->fill(array_merge($defaults, $settings));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Gateway Pembayaran Midtrans (Snap / QRIS / VA)')
                    ->description('Integrasi otomatis pembayaran via Midtrans Snap API.')
                    ->schema([
                        Toggle::make('midtrans_enabled')
                            ->label('Aktifkan Gateway Midtrans')
                            ->default(true)
                            ->columnSpanFull(),

                        Select::make('midtrans_environment')
                            ->label('Environment Sistem')
                            ->options([
                                'sandbox' => 'Sandbox (Testing & Uji Coba)',
                                'production' => 'Production (Live Transaksi Riil)',
                            ])
                            ->default('sandbox')
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('midtrans_merchant_id')
                            ->label('Merchant ID')
                            ->placeholder('G123456789')
                            ->columnSpan(1),

                        TextInput::make('midtrans_client_key')
                            ->label('Client Key')
                            ->placeholder('SB-Mid-client-...')
                            ->columnSpan(1),

                        TextInput::make('midtrans_server_key')
                            ->label('Server Key')
                            ->password()
                            ->revealable()
                            ->placeholder('SB-Mid-server-...')
                            ->columnSpan(1),

                        Toggle::make('midtrans_enable_3ds')
                            ->label('Aktifkan 3D Secure (Kartu Kredit / Debit Online)')
                            ->default(true)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Gateway Pembayaran Xendit (Invoice / E-Wallet)')
                    ->description('Integrasi otomatis alternatif via Xendit API.')
                    ->schema([
                        Toggle::make('xendit_enabled')
                            ->label('Aktifkan Gateway Xendit')
                            ->default(false)
                            ->columnSpanFull(),

                        Select::make('xendit_environment')
                            ->label('Environment Sistem')
                            ->options([
                                'sandbox' => 'Sandbox (Development)',
                                'production' => 'Production (Live)',
                            ])
                            ->default('sandbox')
                            ->columnSpan(1),

                        TextInput::make('xendit_public_key')
                            ->label('Public Key')
                            ->placeholder('xnd_public_...')
                            ->columnSpan(1),

                        TextInput::make('xendit_secret_key')
                            ->label('Secret API Key')
                            ->password()
                            ->revealable()
                            ->placeholder('xnd_development_...')
                            ->columnSpan(1),

                        TextInput::make('xendit_webhook_token')
                            ->label('Webhook Verification Token')
                            ->password()
                            ->revealable()
                            ->placeholder('xnd_webhook_...')
                            ->columnSpan(1),
                    ])->columns(2),

                Section::make('Transfer Bank Manual')
                    ->description('Rekening tujuan untuk transfer manual dan verifikasi struk admin.')
                    ->schema([
                        Toggle::make('manual_transfer_enabled')
                            ->label('Izinkan Metode Transfer Bank Manual')
                            ->default(true)
                            ->columnSpanFull(),

                        TextInput::make('bank_name')
                            ->label('Nama Bank / Lembaga Keuangan')
                            ->placeholder('Bank Central Asia (BCA)')
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('bank_account_number')
                            ->label('Nomor Rekening')
                            ->placeholder('8012345678')
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('bank_account_holder')
                            ->label('Nama Pemilik Rekening (Atas Nama)')
                            ->placeholder('PT TRADING EDU INDONESIA')
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('transfer_instructions')
                            ->label('Instruksi Pembayaran')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Aturan Kedaluwarsa & Kode Unik Pesanan')
                    ->description('Konfigurasi batas waktu pembayaran order dan pembatalan otomatis.')
                    ->schema([
                        TextInput::make('order_expiry_hours')
                            ->label('Masa Berlaku Invoice (Jam)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(168)
                            ->default(24)
                            ->required()
                            ->columnSpan(1),

                        Toggle::make('auto_cancel_expired')
                            ->label('Batalkan Otomatis Pesanan Kadaluwarsa')
                            ->default(true)
                            ->helperText('Pesanan unpaid yang melewati batas waktu akan otomatis dibatalkan.')
                            ->columnSpan(1),

                        Toggle::make('unique_code_enabled')
                            ->label('Aktifkan 3 Digit Kode Unik Pembayaran Manual')
                            ->default(true)
                            ->helperText('Memudahkan rekonsiliasi mutasi rekening otomatis (cth: Rp 500.124).')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        Setting::setGroup('payment', $data);

        Notification::make()
            ->title('Pengaturan Pembayaran Berhasil Disimpan')
            ->body('Konfigurasi payment gateway dan rekening transfer telah diperbarui.')
            ->success()
            ->send();
    }
}
