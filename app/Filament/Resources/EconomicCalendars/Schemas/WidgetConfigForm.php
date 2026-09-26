<?php

namespace App\Filament\Resources\EconomicCalendars\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WidgetConfigForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Lingkup Target Pengguna (Scope)')
                ->description('Tentukan apakah konfigurasi widget TradingView ini berlaku secara global untuk semua customer atau di-override khusus untuk customer tertentu.')
                ->schema([
                    Select::make('customer_id')
                        ->label('Customer / Siswa Spesifik')
                        ->relationship('customer', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('🌐 Global Default (Berlaku untuk Semua Customer)')
                        ->helperText('Kosongkan kolom ini jika ingin menjadikannya pengaturan default seluruh aplikasi customer.'),

                    Toggle::make('is_active')
                        ->label('Status Konfigurasi Aktif')
                        ->default(true)
                        ->helperText('Jika nonaktif, sistem otomatis menggunakan konfigurasi default global.'),
                ])->columns(2),

            Section::make('Tampilan & Dimensi Widget TradingView')
                ->description('Sesuaikan tema visual dan ukuran wadah kalender ekonomi.')
                ->schema([
                    Select::make('color_theme')
                        ->label('Tema Warna (Color Theme)')
                        ->options([
                            'dark' => '🌙 Dark Theme (Direkomendasikan)',
                            'light' => '☀️ Light Theme',
                        ])
                        ->default('dark')
                        ->required(),

                    Toggle::make('is_transparent')
                        ->label('Latar Transparan (is_transparent)')
                        ->default(false)
                        ->helperText('Widget menyatu dengan background container aplikasi tanpa warna solid.'),

                    TextInput::make('width')
                        ->label('Lebar Widget (Width)')
                        ->default('100%')
                        ->required()
                        ->placeholder('100% atau 800px'),

                    TextInput::make('height')
                        ->label('Tinggi Widget (Height)')
                        ->default('650')
                        ->required()
                        ->placeholder('650 atau 100%')
                        ->helperText('Tinggi dalam pixel (rekomendasi: 650) atau persentase.'),
                ])->columns(2),

            Section::make('Bahasa & Filter Kalender Ekonomi')
                ->description('Pengaturan resmi TradingView untuk bahasa dan filtering mata uang serta level dampak rilis.')
                ->schema([
                    Select::make('locale')
                        ->label('Bahasa / Locale')
                        ->options([
                            'en' => 'English (en)',
                            'id_ID' => 'Bahasa Indonesia (id_ID)',
                            'ja' => 'Japanese (ja)',
                            'de' => 'German (de)',
                            'fr' => 'French (fr)',
                            'es' => 'Spanish (es)',
                            'zh_CN' => 'Chinese (zh_CN)',
                        ])
                        ->default('en')
                        ->required()
                        ->helperText('Bahasa tampilan header, deskripsi peristiwa, dan istilah makro.'),

                    Select::make('importance_filter')
                        ->label('Filter Tingkat Dampak (Importance Filter)')
                        ->options([
                            '-1,0,1' => '🟢🟡🔴 Semua Dampak (Low, Medium, & High Impact)',
                            '0,1' => '🟡🔴 Medium & High Impact Saja',
                            '1' => '🔴 High Impact Only (Katalis Volatilitas Utama / NFP / CPI)',
                        ])
                        ->default('-1,0,1')
                        ->required()
                        ->helperText('Filter signifikansi pergerakan harga akibat rilis berita.'),

                    Select::make('currencies')
                        ->label('Filter Mata Uang (Currencies Filter)')
                        ->multiple()
                        ->options([
                            'USD' => '🇺🇸 USD - US Dollar',
                            'EUR' => '🇪🇺 EUR - Euro',
                            'GBP' => '🇬🇧 GBP - British Pound',
                            'JPY' => '🇯🇵 JPY - Japanese Yen',
                            'AUD' => '🇦🇺 AUD - Australian Dollar',
                            'CAD' => '🇨🇦 CAD - Canadian Dollar',
                            'CHF' => '🇨🇭 CHF - Swiss Franc',
                            'NZD' => '🇳🇿 NZD - New Zealand Dollar',
                            'CNY' => '🇨🇳 CNY - Chinese Yuan',
                            'IDR' => '🇮🇩 IDR - Indonesian Rupiah',
                        ])
                        ->default(['USD', 'EUR', 'GBP', 'JPY', 'AUD', 'CAD', 'CHF'])
                        ->columnSpanFull()
                        ->helperText('Pilih mata uang yang peristiwanya ingin ditampilkan pada kalender.'),
                ])->columns(2),
        ]);
    }
}
