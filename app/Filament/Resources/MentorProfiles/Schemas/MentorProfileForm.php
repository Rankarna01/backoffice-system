<?php

namespace App\Filament\Resources\MentorProfiles\Schemas;

use App\Domain\Identity\Models\MentorProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MentorProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun & Identitas Profesional')
                    ->description('Pilih akun pengguna yang terdaftar sebagai mentor, upload foto branding resmi, dan atur profil profesionalnya.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                FileUpload::make('avatar_url')
                                    ->label('Foto Profil Mentor')
                                    ->image()
                                    ->avatar()
                                    ->imageEditor()
                                    ->circleCropper()
                                    ->disk('public')
                                    ->directory('mentors/avatars')
                                    ->maxSize(5120)
                                    ->helperText('Format foto JPG/PNG/WEBP (Maks. 5MB). Akan tampil di kartu mentor, kelas, dan sertifikat.')
                                    ->columnSpan(1),

                                Grid::make(1)
                                    ->schema([
                                        Select::make('user_id')
                                            ->label('Akun Pengguna')
                                            ->relationship('user', 'name')
                                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})")
                                            ->searchable(['name', 'email'])
                                            ->preload()
                                            ->required()
                                            ->unique(MentorProfile::class, 'user_id', ignoreRecord: true)
                                            ->helperText('Pilih akun pengguna yang terdaftar. Akun ini harus memiliki role mentor.'),

                                        TextInput::make('headline')
                                            ->label('Headline Profesional')
                                            ->placeholder('Contoh: Certified Financial Technician & Full-Time Price Action Trader')
                                            ->required()
                                            ->maxLength(255)
                                            ->helperText('Gelar atau julukan singkat yang tampil di bawah nama mentor.'),
                                    ])
                                    ->columnSpan(2),
                            ]),

                        Textarea::make('bio')
                            ->label('Biografi & Rekam Jejak')
                            ->placeholder('Tuliskan rekam jejak, spesialisasi analisa pasar, dan filosofi trading mentor...')
                            ->rows(4)
                            ->maxLength(1500)
                            ->columnSpanFull(),
                    ]),

                Section::make('Metrik Portofolio & Rekam Jejak Trading')
                    ->description('Kredensial performa trading nyata, statistik akurasi, dan tautan audit live akun untuk membangun kepercayaan murid.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('experience_years')
                                    ->label('Pengalaman Trading')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(60)
                                    ->suffix('Tahun')
                                    ->placeholder('8')
                                    ->helperText('Lama aktif di industri trading finansial.'),

                                TextInput::make('trading_style')
                                    ->label('Gaya / Metodologi Trading')
                                    ->placeholder('Contoh: Price Action & SMC Swing')
                                    ->maxLength(100)
                                    ->helperText('Gaya analisa teknikal utama yang diajarkan.'),

                                TextInput::make('win_rate')
                                    ->label('Estimasi Win Rate / ROI')
                                    ->numeric()
                                    ->suffix('%')
                                    ->placeholder('68.5')
                                    ->helperText('Akurasi setup atau performa tahunan.'),
                            ]),

                        TextInput::make('portfolio_url')
                            ->label('Tautan Portfolio / Track Record Terverifikasi')
                            ->url()
                            ->placeholder('https://www.myfxbook.com/members/... atau https://tradingview.com/u/...')
                            ->maxLength(255)
                            ->helperText('Tautan publik ke akun trading live yang terverifikasi (Myfxbook, MQL5, TradingView, atau audit portofolio).')
                            ->columnSpanFull(),

                        TagsInput::make('certifications')
                            ->label('Sertifikasi & Lisensi Profesional')
                            ->placeholder('Ketik sertifikasi lalu tekan Enter')
                            ->suggestions([
                                'CFTe (Certified Financial Technician)',
                                'CMT (Chartered Market Technician)',
                                'WPP (Wakil Pialang Berjangka)',
                                'WPPE (Wakil Perantara Pedagang Efek)',
                                'CFA (Chartered Financial Analyst)',
                                'RFP (Registered Financial Planner)',
                                'Prop Firm Funded Master Trader',
                            ])
                            ->helperText('Sertifikasi atau lisensi finansial yang dimiliki untuk menjamin kualitas materi pengajaran.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Galeri Portofolio & Bukti Analisa (Trade Setups Showcase)')
                    ->description('Koleksi contoh analisa pasar terbaik, hasil trade swing/scalping, dan dokumentasi setup mentor.')
                    ->schema([
                        Repeater::make('portfolio_items')
                            ->label('Daftar Portofolio & Hasil Setup')
                            ->itemLabel(fn (array $state): ?string => ($state['title'] ?? null)
                                ? ($state['title'] . (($state['pnl'] ?? null) ? " — {$state['pnl']}" : ''))
                                : 'Setup Portofolio Baru')
                            ->collapsible()
                            ->reorderable()
                            ->addActionLabel('+ Tambah Portofolio Setup')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Judul Analisa / Pencapaian')
                                            ->placeholder('Contoh: XAUUSD Rally +1,200 Pips Setup')
                                            ->required()
                                            ->columnSpan(2),

                                        TextInput::make('pnl')
                                            ->label('Hasil PnL / Gain')
                                            ->placeholder('Contoh: +28.5% ROI / +1,200 Pips')
                                            ->columnSpan(1),
                                    ]),

                                Textarea::make('description')
                                    ->label('Ringkasan Strategi & Eksekusi')
                                    ->placeholder('Uraikan konfirmasi entry, risk-to-reward ratio 1:4, dan manajemen posisi...')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Grid::make(2)
                                    ->schema([
                                        FileUpload::make('proof_image')
                                            ->label('Gambar Chart / Bukti Eksekusi')
                                            ->image()
                                            ->disk('public')
                                            ->directory('mentors/portfolios')
                                            ->maxSize(5120)
                                            ->helperText('Screenshot chart sebelum dan sesudah setup running.'),

                                        TextInput::make('proof_url')
                                            ->label('Tautan Chart (TradingView / Snapshot)')
                                            ->url()
                                            ->placeholder('https://www.tradingview.com/x/...'),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Keahlian & Pengaturan Visibilitas')
                    ->description('Tentukan tag keahlian trading serta urutan penampilan di katalog publik.')
                    ->schema([
                        TagsInput::make('expertise')
                            ->label('Spesialisasi Trading')
                            ->placeholder('Ketik keahlian lalu tekan Enter')
                            ->suggestions([
                                'Price Action',
                                'Smart Money Concepts (SMC)',
                                'Forex Major Pairs',
                                'Gold (XAUUSD)',
                                'Cryptocurrency Derivatives',
                                'Indeks Saham Global',
                                'Risk Management',
                                'Scalping & Intraday',
                                'Order Flow & Tape Reading',
                                'Macro Economics & Fundamental',
                                'Swing Trading',
                                'Algorithmic Trading',
                            ])
                            ->helperText('Tag keahlian ini akan muncul di kartu mentor dan filter pencarian kursus.')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_featured')
                                    ->label('Mentor Unggulan (Featured)')
                                    ->helperText('Jika aktif, profil mentor akan diprioritaskan di landing page & banner.')
                                    ->default(false),

                                TextInput::make('sort_order')
                                    ->label('Urutan Tampilan')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->helperText('Nomor urutan (angka lebih kecil ditampilkan lebih atas).'),
                            ]),
                    ]),

                Section::make('Tautan Saluran Komunitas & Media Sosial')
                    ->description('Kanal resmi mentor agar member dapat bergabung ke grup analisis dan media edukasi.')
                    ->schema([
                        KeyValue::make('social_links')
                            ->label('Tautan Media Sosial')
                            ->keyLabel('Platform')
                            ->keyPlaceholder('instagram / youtube / telegram / twitter')
                            ->valueLabel('Username / Tautan URL')
                            ->valuePlaceholder('@username atau https://...')
                            ->reorderable()
                            ->helperText('Daftar saluran publik mentor (misal Instagram, YouTube, Telegram Channel).')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}

