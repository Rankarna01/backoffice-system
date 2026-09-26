<?php

namespace App\Filament\Resources\MarketNews\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class MarketNewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Market News Details')
                    ->tabs([
                        // Tab 1: Headline & Parameter Berita
                        Tab::make('Headline & Parameter Berita')
                            ->icon('heroicon-m-newspaper')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul Berita Pasar Finansial')
                                    ->placeholder('Contoh: The Fed Pangkas Suku Bunga 50 Bps, Emas Cetak Rekor All-Time High Baru')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, callable $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                                    ->columnSpanFull()
                                    ->helperText('Judul headline berita yang jelas, akurat, dan informatif bagi para trader.'),

                                TextInput::make('slug')
                                    ->label('URL Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique('market_news', 'slug', ignoreRecord: true)
                                    ->columnSpanFull()
                                    ->helperText('Alamat URL unik ramah mesin pencari.'),

                                Grid::make(3)
                                    ->schema([
                                        Select::make('category')
                                            ->label('Kategori Berita')
                                            ->options([
                                                'central_banks' => 'Kebijakan Bank Sentral (Fed, ECB, BOJ)',
                                                'commodities' => 'Komoditas (Emas, Minyak, Perak)',
                                                'forex' => 'Forex Currencies (Mata Uang G10)',
                                                'crypto' => 'Kripto Derivatif (Bitcoin, Altcoin)',
                                                'indices' => 'Indeks Saham (Wall Street, Asia)',
                                                'macro_economy' => 'Ekonomi Makro & Geopolitik',
                                            ])
                                            ->default('central_banks')
                                            ->required()
                                            ->helperText('Kategori topik pembahasan utama berita.'),

                                        Select::make('impact_level')
                                            ->label('Tingkat Dampak Volatilitas')
                                            ->options([
                                                'high' => 'High Impact (Volatilitas Ekstrem)',
                                                'medium' => 'Medium Impact (Pergerakan Sedang)',
                                                'low' => 'Low Impact (Sentimen Ringan)',
                                            ])
                                            ->default('high')
                                            ->required()
                                            ->helperText('Tingkat pengaruh berita terhadap fluktuasi harga pasar.'),

                                        Select::make('sentiment')
                                            ->label('Sentimen Pasar')
                                            ->options([
                                                'bullish' => 'Bullish (Memicu Kenaikan)',
                                                'bearish' => 'Bearish (Memicu Penurunan)',
                                                'neutral' => 'Neutral (Berdampak Berimbang)',
                                            ])
                                            ->default('neutral')
                                            ->required(),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('source')
                                            ->label('Sumber Berita / Kantor Berita')
                                            ->placeholder('Contoh: Bloomberg / Reuters / Tim Analis Internal')
                                            ->helperText('Nama rujukan penyedia berita primer.'),

                                        TextInput::make('source_url')
                                            ->label('Tautan Rujukan Asli (Source URL)')
                                            ->placeholder('https://www.bloomberg.com/news/...')
                                            ->url()
                                            ->helperText('Tautan ke portal berita eksternal (opsional).'),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        Select::make('author_id')
                                            ->label('Editor / Jurnalis Analis')
                                            ->relationship('author', 'name', fn ($query) => $query->whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'mentor'])))
                                            ->searchable()
                                            ->preload()
                                            ->default(fn () => auth()->id())
                                            ->helperText('Penyusun atau editor artikel berita ini.'),

                                        DateTimePicker::make('published_at')
                                            ->label('Waktu Publikasi')
                                            ->default(now())
                                            ->helperText('Waktu tayang berita kepada pembaca.'),
                                    ]),

                                TagsInput::make('related_symbols')
                                    ->label('Simbol Aset & Pair Terkait')
                                    ->placeholder('Ketik simbol pair lalu tekan Enter (misal: XAUUSD, EURUSD, DXY)')
                                    ->suggestions([
                                        'XAUUSD',
                                        'EURUSD',
                                        'GBPUSD',
                                        'USDJPY',
                                        'DXY',
                                        'BTCUSDT',
                                        'ETHUSDT',
                                        'USOIL',
                                        'US30',
                                        'NAS100',
                                    ])
                                    ->columnSpanFull()
                                    ->helperText('Pair yang berpotensi terpengaruh langsung oleh peristiwa berita ini.'),

                                Grid::make(3)
                                    ->schema([
                                        Toggle::make('is_breaking')
                                            ->label('Flash / Breaking News')
                                            ->default(false)
                                            ->helperText('Kirimkan notifikasi kilat instan ke aplikasi member.'),

                                        Toggle::make('is_featured')
                                            ->label('Headline Berita Utama')
                                            ->default(false)
                                            ->helperText('Tampilkan di carousel slider halaman utama pasar.'),

                                        Select::make('status')
                                            ->label('Status Publikasi')
                                            ->options([
                                                'draft' => 'Draft (Draf Editor)',
                                                'published' => 'Published (Tayang)',
                                                'archived' => 'Archived (Diarsipkan)',
                                            ])
                                            ->default('published')
                                            ->required(),
                                    ]),
                            ]),

                        // Tab 2: Isi Berita & Media Visual
                        Tab::make('Isi Berita & Media Visual')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                TextInput::make('cover_image_url')
                                    ->label('URL Gambar Header / Thumbnail Berita')
                                    ->placeholder('https://pub-r2.tradingedu.dev/marketing/banners/... atau URL gambar')
                                    ->columnSpanFull()
                                    ->helperText('Foto atau grafis pendukung berita yang tersimpan di R2 atau CDN.'),

                                Textarea::make('summary')
                                    ->label('Ringkasan Cepat Berita (Lead / TL;DR)')
                                    ->placeholder('Tuliskan 2-3 kalimat rangkuman inti peristiwa sebelum artikel lengkap...')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->helperText('Teks pembuka berita yang akan tampil di push alert dan feed portal.'),

                                RichEditor::make('content')
                                    ->label('Isi Berita Finansial Lengkap')
                                    ->placeholder('Tuliskan ulasan detail peristiwa berita, kutipan pejabat bank sentral, data ekonomi, dan dampaknya bagi pasar...')
                                    ->required()
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('r2-mock/market-news')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
