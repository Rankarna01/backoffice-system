<?php

namespace App\Filament\Resources\MarketOutlooks\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class MarketOutlookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Market Outlook Details')
                    ->tabs([
                        // Tab 1: Informasi Utama & Parameter Makro
                        Tab::make('Informasi Utama & Makro')
                            ->icon('heroicon-m-presentation-chart-line')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul Ulasan Market Outlook')
                                    ->placeholder('Contoh: Weekly Outlook: Emas Mengincar All-Time High $2.700 Menjelang FOMC')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, callable $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                                    ->columnSpanFull()
                                    ->helperText('Judul headline artikel analisis makro ekonomi pasar finansial.'),

                                TextInput::make('slug')
                                    ->label('URL Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique('market_outlooks', 'slug', ignoreRecord: true)
                                    ->columnSpanFull()
                                    ->helperText('Alamat URL ramah SEO untuk artikel outlook.'),

                                Grid::make(3)
                                    ->schema([
                                        Select::make('market_category')
                                            ->label('Kategori Pasar Utama')
                                            ->options([
                                                'commodities' => 'Komoditas (Emas / Logam / Minyak)',
                                                'forex' => 'Forex Currencies (Mata Uang G10)',
                                                'crypto' => 'Kripto Derivatif (Bitcoin & Altcoins)',
                                                'indices' => 'Indeks Saham Global (Wall Street & Eropa)',
                                                'multi_asset' => 'Multi-Asset Macro (Ulasan Lintas Pasar)',
                                            ])
                                            ->default('multi_asset')
                                            ->required()
                                            ->helperText('Klasifikasi pasar yang dibahas secara mendominasi.'),

                                        Select::make('sentiment')
                                            ->label('Bias Sentimen Pasar')
                                            ->options([
                                                'bullish' => 'Bullish (Tren Menguat)',
                                                'bearish' => 'Bearish (Tren Melemah)',
                                                'neutral' => 'Neutral (Konsolidasi / Sideways)',
                                                'volatile' => 'Volatile (Fluktuasi Tinggi / Dua Arah)',
                                            ])
                                            ->default('neutral')
                                            ->required()
                                            ->helperText('Sentimen umum pasar untuk horizon waktu ini.'),

                                        Select::make('time_horizon')
                                            ->label('Cakupan Horizon Waktu')
                                            ->options([
                                                'weekly' => 'Mingguan (Weekly Outlook)',
                                                'monthly' => 'Bulanan (Monthly Macro)',
                                                'quarterly' => 'Kuartalan (Q Outlook)',
                                                'special_report' => 'Laporan Khusus (Special Event)',
                                            ])
                                            ->default('weekly')
                                            ->required(),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        Select::make('mentor_id')
                                            ->label('Analis / Mentor Penulis')
                                            ->relationship('mentor', 'name', fn ($query) => $query->whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'mentor'])))
                                            ->searchable()
                                            ->preload()
                                            ->default(fn () => auth()->id())
                                            ->helperText('Pakar atau analis yang menyusun ulasan ini.'),

                                        DateTimePicker::make('published_at')
                                            ->label('Waktu Publikasi / Penjadwalan')
                                            ->default(now())
                                            ->helperText('Waktu rilis tayang artikel ke member portal.'),
                                    ]),

                                TagsInput::make('featured_pairs')
                                    ->label('Instrumen & Pair yang Dibahas')
                                    ->placeholder('Ketik simbol pair lalu tekan Enter (misal: XAUUSD, EURUSD, DXY, BTCUSDT)')
                                    ->suggestions([
                                        'XAUUSD',
                                        'EURUSD',
                                        'GBPUSD',
                                        'USDJPY',
                                        'DXY',
                                        'USOIL',
                                        'BTCUSDT',
                                        'ETHUSDT',
                                        'US30',
                                        'NAS100',
                                        'SPX500',
                                    ])
                                    ->columnSpanFull()
                                    ->helperText('Daftar pair yang menjadi fokus bahasan dalam ulasan ini.'),

                                Grid::make(2)
                                    ->schema([
                                        Toggle::make('is_premium')
                                            ->label('Khusus Member VIP / Premium')
                                            ->default(true)
                                            ->helperText('Jika aktif, ulasan lengkap hanya dapat diakses oleh member yang berlangganan VIP.'),

                                        Select::make('status')
                                            ->label('Status Publikasi')
                                            ->options([
                                                'draft' => 'Draft (Draf Penulis)',
                                                'published' => 'Published (Tayang di Portal)',
                                                'archived' => 'Archived (Diarsipkan)',
                                            ])
                                            ->default('published')
                                            ->required(),
                                    ]),
                            ]),

                        // Tab 2: Konten Analisa & Media Chart
                        Tab::make('Konten Analisa & Chart')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                TextInput::make('cover_image_url')
                                    ->label('URL Gambar Cover / Screenshot Analisa')
                                    ->placeholder('https://pub-r2.tradingedu.dev/courses/thumbnails/... atau URL gambar')
                                    ->columnSpanFull()
                                    ->helperText('Tautan gambar header artikel yang tersimpan di Cloudflare R2 atau TradingView.'),

                                Textarea::make('summary')
                                    ->label('Ringkasan Eksekutif (Executive Summary)')
                                    ->placeholder('Tuliskan 2-3 kalimat rangkuman gambaran besar pasar untuk preview pembaca...')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->helperText('Teks preview yang akan tampil di feed kartu artikel sebelum artikel dibuka.'),

                                RichEditor::make('content')
                                    ->label('Isi Ulasan Analisa Makro Lengkap')
                                    ->placeholder('Uraikan latar belakang fundamental suku bunga, data inflasi CPI, analisa teknikal market structure H4/D1, dan proyeksi pekan ini...')
                                    ->required()
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('r2-mock/market-outlooks')
                                    ->columnSpanFull(),
                            ]),

                        // Tab 3: Poin Kunci & Peta Level Support/Resistance
                        Tab::make('Key Takeaways & Key Levels')
                            ->icon('heroicon-m-table-cells')
                            ->schema([
                                TagsInput::make('key_takeaways')
                                    ->label('Poin-Poin Kunci (Key Takeaways)')
                                    ->placeholder('Ketik poin kesimpulan lalu tekan Enter...')
                                    ->columnSpanFull()
                                    ->helperText('Daftar poin kesimpulan cepat yang memudahkan murid menangkap inti pembahasan.'),

                                Repeater::make('support_resistance_levels')
                                    ->label('Peta Level Kunci Harga (Key Support & Resistance Levels)')
                                    ->addActionLabel('Tambah Level Instrumen')
                                    ->collapsible()
                                    ->cloneable()
                                    ->schema([
                                        Grid::make(4)
                                            ->schema([
                                                TextInput::make('pair')
                                                    ->label('Pair / Instrumen')
                                                    ->placeholder('XAUUSD')
                                                    ->required(),

                                                TextInput::make('support')
                                                    ->label('Key Support Zone')
                                                    ->placeholder('$2,630 - $2,635')
                                                    ->required(),

                                                TextInput::make('resistance')
                                                    ->label('Key Resistance Zone')
                                                    ->placeholder('$2,685 - $2,700')
                                                    ->required(),

                                                Select::make('bias')
                                                    ->label('Sentimen Bias')
                                                    ->options([
                                                        'BULLISH' => 'BULLISH',
                                                        'BEARISH' => 'BEARISH',
                                                        'NEUTRAL' => 'NEUTRAL',
                                                    ])
                                                    ->default('BULLISH')
                                                    ->required(),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
