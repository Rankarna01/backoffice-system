<?php

namespace App\Filament\Resources\Lessons\Schemas;

use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\Lesson;
use App\Domain\Learning\Models\Module;
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

class LessonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Lesson Details')
                    ->tabs([
                        // Tab 1: Informasi Materi & Penempatan
                        Tab::make('Informasi Materi & Penempatan')
                            ->icon('heroicon-m-book-open')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('course_id')
                                            ->label('Kursus Induk')
                                            ->relationship('course', 'title')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->live()
                                            ->default(fn () => request()->query('course_id'))
                                            ->afterStateUpdated(fn (callable $set) => $set('module_id', null))
                                            ->helperText('Pilih kursus induk materi ini.'),

                                        Select::make('module_id')
                                            ->label('Bab / Modul')
                                            ->relationship(
                                                'module',
                                                'title',
                                                fn ($query, callable $get) => $query->when(
                                                    $get('course_id'),
                                                    fn ($q, $courseId) => $q->where('course_id', $courseId)
                                                )
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->default(fn () => request()->query('module_id'))
                                            ->helperText('Pilih bab/modul tempat pelajaran ini berada.'),
                                    ]),

                                TextInput::make('title')
                                    ->label('Judul Pelajaran / Materi')
                                    ->placeholder('Contoh: Membaca Indikasi Break of Structure (BOS) Valid')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, ?string $state, callable $set) {
                                        if ($operation === 'create' && filled($state)) {
                                            $set('slug', Str::slug($state));
                                        }
                                    })
                                    ->helperText('Judul materi yang akan muncul pada kurikulum aplikasi murid.'),

                                TextInput::make('slug')
                                    ->label('Slug URL Pelajaran')
                                    ->placeholder('membaca-indikasi-bos-valid')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Slug otomatis terbentuk dari judul untuk identifikasi rute pelajaran.'),

                                Radio::make('type')
                                    ->label('Format / Tipe Materi')
                                    ->options([
                                        'video' => 'Video Pembelajaran (YouTube / Vimeo / Video CDN)',
                                        'article' => 'Artikel Teks & Studi Kasus (Bacaan Interaktif)',
                                        'file' => 'Materi Lampiran / Lembar Tugas',
                                    ])
                                    ->default('video')
                                    ->live()
                                    ->required(),
                            ]),

                        // Tab 2: Konten Pelajaran & Media
                        Tab::make('Konten Pelajaran & Media')
                            ->icon('heroicon-m-film')
                            ->schema([
                                Section::make('Video Streaming Player')
                                    ->icon('heroicon-m-play')
                                    ->description('Konfigurasi pemutar video materi pembelajaran.')
                                    ->visible(fn (callable $get) => $get('type') === 'video')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Select::make('video_provider')
                                                    ->label('Penyedia Video')
                                                    ->options([
                                                        'youtube' => 'YouTube (Unlisted / Public)',
                                                        'vimeo' => 'Vimeo Pro / OTT',
                                                        'custom' => 'Direct Video URL (MP4 / HLS / CDN)',
                                                    ])
                                                    ->default('youtube')
                                                    ->required(fn (callable $get) => $get('type') === 'video'),

                                                TextInput::make('video_ref')
                                                    ->label('Tautan Video / Video ID')
                                                    ->placeholder('https://www.youtube.com/watch?v=... atau Video ID')
                                                    ->columnSpan(2)
                                                    ->required(fn (callable $get) => $get('type') === 'video')
                                                    ->helperText('Masukkan tautan video YouTube/Vimeo atau file MP4 yang akan ditayangkan.'),
                                            ]),

                                        TextInput::make('duration_seconds')
                                            ->label('Durasi Video (Detik)')
                                            ->numeric()
                                            ->placeholder('Contoh: 720 (artinya 12 menit)')
                                            ->helperText('Masukkan total durasi video dalam satuan detik (misal: 600 untuk 10 menit).'),
                                    ]),

                                Section::make('Catatan Silabus & Ulasan Pelajaran')
                                    ->icon('heroicon-m-document-text')
                                    ->description('Ringkasan materi, checklist pemahaman, atau catatan setup chart untuk murid.')
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Isi Materi Pelajaran / Rangkuman Teori')
                                            ->placeholder("Tuliskan poin-poin penting yang perlu dicatat murid, prasyarat entry chart, atau ringkasan materi pelajaran...")
                                            ->rows(8)
                                            ->columnSpanFull()
                                            ->helperText('Catatan yang akan tampil di bawah pemutar video atau sebagai artikel pembelajaran utama.'),
                                    ]),
                            ]),

                        // Tab 3: Pengaturan Akses & Publikasi
                        Tab::make('Pengaturan Akses & Publikasi')
                            ->icon('heroicon-m-cog-6-tooth')
                            ->schema([
                                Section::make('Hak Akses & Kelulusan')
                                    ->icon('heroicon-m-lock-open')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Toggle::make('is_preview')
                                                    ->label('Akses Preview Gratis (Free Preview)')
                                                    ->helperText('Jika aktif, calon murid dapat menonton pelajaran ini gratis tanpa mendaftar/membayar kursus.')
                                                    ->default(false),

                                                Toggle::make('is_required')
                                                    ->label('Wajib Diselesaikan (Sequential Requirement)')
                                                    ->helperText('Murid wajib menuntaskan materi ini sebelum bisa melanjutkan ke bab/materi berikutnya.')
                                                    ->default(true),
                                            ]),
                                    ]),

                                Section::make('Urutan & Status Tayang')
                                    ->icon('heroicon-m-queue-list')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('sort_order')
                                                    ->label('Urutan Materi (Sort Order)')
                                                    ->numeric()
                                                    ->default(1)
                                                    ->required()
                                                    ->helperText('Urutan pemutaran materi dalam bab modul ini (1, 2, 3, dst).'),

                                                Select::make('status')
                                                    ->label('Status Publikasi')
                                                    ->options([
                                                        'draft' => 'Draft (Draf Penulis)',
                                                        'published' => 'Published (Aktif & Tayang)',
                                                        'archived' => 'Archived (Diarsipkan)',
                                                    ])
                                                    ->default('published')
                                                    ->required(),
                                            ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
