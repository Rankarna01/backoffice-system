<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Domain\Learning\Models\Category;
use App\Domain\Learning\Models\Course;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Course Details')
                    ->tabs([
                        // Tab 1: Informasi Dasar & Konten
                        Tab::make('Informasi Dasar')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('mentor_id')
                                            ->label('Mentor / Instruktur')
                                            ->relationship('mentor', 'name')
                                            ->options(fn () => User::role('mentor')->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->helperText('Pilih mentor instruktur penanggung jawab kurikulum.'),

                                        Select::make('category_id')
                                            ->label('Kategori Kursus')
                                            ->relationship('category', 'name', fn ($query) => $query->where('type', 'course')->where('is_active', true))
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->helperText('Kelompokkan materi berdasarkan bidang trading.'),
                                    ]),

                                TextInput::make('title')
                                    ->label('Judul Kursus')
                                    ->placeholder('Contoh: Smart Money Concept & Liquidity Mastery')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, ?string $state, callable $set) {
                                        if ($operation === 'create' && filled($state)) {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),

                                TextInput::make('slug')
                                    ->label('Slug URL')
                                    ->placeholder('smart-money-concept-mastery')
                                    ->required()
                                    ->unique(Course::class, 'slug', ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText('Slug otomatis terbentuk dari judul untuk alamat URL halaman kursus.'),

                                TextInput::make('subtitle')
                                    ->label('Subtitle / Tagline Ringkas')
                                    ->placeholder('Ringkasan 1 kalimat yang memikat calon trader')
                                    ->maxLength(255),

                                Textarea::make('description')
                                    ->label('Deskripsi Lengkap & Silabus')
                                    ->placeholder('Tuliskan rincian apa yang akan dipelajari, prasyarat, dan hasil akhir belajar peserta...')
                                    ->rows(6)
                                    ->columnSpanFull(),

                                TextInput::make('preview_video_ref')
                                    ->label('Tautan Video Trailer / Preview')
                                    ->placeholder('https://www.youtube.com/watch?v=... atau ID video')
                                    ->helperText('Video trailer gratis yang dapat ditonton sebelum mendaftar / membeli.'),
                            ]),

                        // Tab 2: Model Akses & Harga
                        Tab::make('Model Akses & Harga')
                            ->icon('heroicon-m-banknotes')
                            ->schema([
                                Radio::make('access_type')
                                    ->label('Tipe Akses Kursus')
                                    ->options([
                                        'free' => 'Gratis (Free Access)',
                                        'paid' => 'Sekali Bayar (One-time Purchase)',
                                        'subscription' => 'Khusus Paket Langganan VIP/Pro',
                                    ])
                                    ->default('paid')
                                    ->live()
                                    ->required(),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('price')
                                            ->label('Harga Jual')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0)
                                            ->visible(fn (callable $get) => $get('access_type') === 'paid')
                                            ->required(fn (callable $get) => $get('access_type') === 'paid')
                                            ->helperText('Nominal harga yang dibayar peserta untuk akses seumur hidup.'),

                                        TextInput::make('compare_at_price')
                                            ->label('Harga Coret / Asli')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->visible(fn (callable $get) => $get('access_type') === 'paid')
                                            ->helperText('Harga sebelum diskon untuk menarik minat promosi.'),
                                    ]),

                                TextInput::make('currency')
                                    ->label('Mata Uang')
                                    ->default('IDR')
                                    ->disabled()
                                    ->dehydrated(),
                            ]),

                        // Tab 3: Pengaturan Belajar & Sertifikasi
                        Tab::make('Aturan & Sertifikasi')
                            ->icon('heroicon-m-academic-cap')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('level')
                                            ->label('Tingkat Kesulitan')
                                            ->options([
                                                'beginner' => 'Pemula (Beginner)',
                                                'intermediate' => 'Menengah (Intermediate)',
                                                'advanced' => 'Tingkat Lanjut (Advanced)',
                                            ])
                                            ->default('beginner')
                                            ->required(),

                                        Select::make('language')
                                            ->label('Bahasa Pengantar')
                                            ->options([
                                                'id' => 'Bahasa Indonesia',
                                                'en' => 'English',
                                            ])
                                            ->default('id')
                                            ->required(),
                                    ]),

                                Section::make('Aturan Belajar Siswa')
                                    ->schema([
                                        Toggle::make('is_sequential')
                                            ->label('Urutan Materi Ketat (Sequential Learning)')
                                            ->helperText('Jika aktif, murid wajib menyelesaikan setiap bab dan kuis secara berurutan; materi berikutnya terkunci.')
                                            ->default(false),

                                        Toggle::make('has_certificate')
                                            ->label('Terbitkan Sertifikat Kelulusan')
                                            ->helperText('Jika aktif, sertifikat berlisensi otomatis terbit dengan ID unik saat peserta menyelesaikan semua materi dan lulus kuis.')
                                            ->default(true),
                                    ]),
                            ]),

                        // Tab 4: Status & Publikasi
                        Tab::make('Status & Publikasi')
                            ->icon('heroicon-m-clock')
                            ->schema([
                                Select::make('status')
                                    ->label('Status Kursus')
                                    ->options([
                                        'draft' => 'Draft (Draf Awal)',
                                        'in_review' => 'In Review (Menunggu Persetujuan Admin)',
                                        'published' => 'Published (Aktif & Tayang Publik)',
                                        'unpublished' => 'Unpublished (Ditarik Sementara)',
                                        'archived' => 'Archived (Diarsipkan)',
                                    ])
                                    ->default('draft')
                                    ->required(),

                                DateTimePicker::make('published_at')
                                    ->label('Tanggal Publikasi')
                                    ->helperText('Waktu tayang publik. Kosongkan bila belum dipublikasikan.'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
