<?php

namespace App\Filament\Resources\Quizzes\Schemas;

use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\Module;
use App\Domain\Learning\Models\Quiz;
use Filament\Forms\Components\Repeater;
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

class QuizForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Quiz Details')
                    ->tabs([
                        // Tab 1: Informasi Kuis & Penempatan
                        Tab::make('Informasi Kuis & Penempatan')
                            ->icon('heroicon-m-information-circle')
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
                                            ->helperText('Pilih kursus induk tempat kuis evaluasi ini berada.'),

                                        Select::make('module_id')
                                            ->label('Bab / Modul (Opsional)')
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
                                            ->default(fn () => request()->query('module_id'))
                                            ->placeholder('Kuis Ujian Akhir Kursus (Final Exam)')
                                            ->helperText('Pilih bab jika kuis per-modul, atau kosongkan untuk Ujian Akhir Kursus.'),
                                    ]),

                                TextInput::make('title')
                                    ->label('Judul Kuis Evaluasi')
                                    ->placeholder('Contoh: Kuis Evaluasi Pemahaman Market Structure & BOS')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Judul kuis yang akan tampil pada kurikulum dan halaman belajar murid.'),

                                Textarea::make('instructions')
                                    ->label('Petunjuk & Instruksi Pengerjaan')
                                    ->placeholder('Tuliskan petunjuk pengerjaan kuis, ketentuan passing score, dan aturan pengerjaan bagi murid...')
                                    ->rows(4)
                                    ->columnSpanFull()
                                    ->helperText('Instruksi awal yang dibaca murid sebelum menekan tombol mulai kuis.'),
                            ]),

                        // Tab 2: Pertanyaan & Kunci Jawaban
                        Tab::make('Pertanyaan & Kunci Jawaban')
                            ->icon('heroicon-m-question-mark-circle')
                            ->schema([
                                Repeater::make('questions')
                                    ->relationship('questions')
                                    ->label('Daftar Soal Pertanyaan Kuis')
                                    ->addActionLabel('Tambah Soal Baru')
                                    ->collapsible()
                                    ->cloneable()
                                    ->itemLabel(fn (array $state): ?string => filled($state['question'] ?? null)
                                        ? Str::limit(strip_tags($state['question']), 60)
                                        : 'Soal Baru')
                                    ->schema([
                                        Textarea::make('question')
                                            ->label('Teks Pertanyaan / Studi Kasus')
                                            ->placeholder('Tuliskan pertanyaan kuis atau deskripsi setup chart trading...')
                                            ->required()
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Grid::make(3)
                                            ->schema([
                                                Select::make('type')
                                                    ->label('Tipe Soal')
                                                    ->options([
                                                        'single' => 'Pilihan Ganda (1 Jawaban Benar)',
                                                        'multiple' => 'Pilihan Ganda (Multi Jawaban)',
                                                        'true_false' => 'Benar / Salah (True / False)',
                                                    ])
                                                    ->default('single')
                                                    ->required(),

                                                TextInput::make('points')
                                                    ->label('Bobot Poin')
                                                    ->numeric()
                                                    ->default(10)
                                                    ->required(),

                                                TextInput::make('sort_order')
                                                    ->label('Nomor Urut')
                                                    ->numeric()
                                                    ->default(1),
                                            ]),

                                        Textarea::make('explanation')
                                            ->label('Pembahasan Jawaban (Feedback Murid)')
                                            ->placeholder('Jelaskan mengapa jawaban tersebut benar dan apa konsep kunci trading yang mendasarinya...')
                                            ->rows(2)
                                            ->columnSpanFull()
                                            ->helperText('Pembahasan akan ditampilkan kepada murid setelah menyelesaikan kuis.'),

                                        Section::make('Pilihan Opsi Jawaban')
                                            ->schema([
                                                Repeater::make('options')
                                                    ->relationship('options')
                                                    ->label('Pilihan Jawaban')
                                                    ->addActionLabel('Tambah Opsi Jawaban')
                                                    ->defaultItems(2)
                                                    ->schema([
                                                        Grid::make(4)
                                                            ->schema([
                                                                TextInput::make('label')
                                                                    ->label('Teks Pilihan')
                                                                    ->placeholder('Pernyataan jawaban...')
                                                                    ->required()
                                                                    ->columnSpan(2),

                                                                Toggle::make('is_correct')
                                                                    ->label('Kunci Jawaban Benar')
                                                                    ->default(false)
                                                                    ->inline(false),

                                                                TextInput::make('sort_order')
                                                                    ->label('Urutan')
                                                                    ->numeric()
                                                                    ->default(1),
                                                            ]),
                                                    ]),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        // Tab 3: Aturan Skor, Waktu & Publikasi
                        Tab::make('Aturan Skor, Waktu & Publikasi')
                            ->icon('heroicon-m-cog-6-tooth')
                            ->schema([
                                Section::make('Aturan Kelulusan & Batasan Kuis')
                                    ->icon('heroicon-m-academic-cap')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('passing_score')
                                                    ->label('Passing Score (Nilai Lulus)')
                                                    ->numeric()
                                                    ->default(70)
                                                    ->suffix('%')
                                                    ->required()
                                                    ->helperText('Persentase skor minimum untuk dinyatakan lulus kuis.'),

                                                TextInput::make('time_limit_minutes')
                                                    ->label('Batas Waktu Pengerjaan')
                                                    ->numeric()
                                                    ->suffix('menit')
                                                    ->placeholder('Tanpa batas waktu')
                                                    ->helperText('Batas waktu pengerjaan. Kosongkan jika tanpa batas waktu.'),

                                                TextInput::make('max_attempts')
                                                    ->label('Batas Percobaan Mengulang')
                                                    ->numeric()
                                                    ->placeholder('Tanpa batas')
                                                    ->helperText('Maksimal murid mengulang kuis. Kosongkan jika tak terbatas.'),
                                            ]),
                                    ]),

                                Section::make('Opsi Pengacakan & Transparansi')
                                    ->icon('heroicon-m-arrows-right-left')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Toggle::make('shuffle_questions')
                                                    ->label('Acak Urutan Soal')
                                                    ->default(false),

                                                Toggle::make('shuffle_options')
                                                    ->label('Acak Urutan Opsi Jawaban')
                                                    ->default(false),

                                                Toggle::make('show_answers')
                                                    ->label('Tampilkan Kunci & Pembahasan')
                                                    ->default(true)
                                                    ->helperText('Tampilkan penjelasan jawaban setelah murid mengirimkan kuis.'),
                                            ]),
                                    ]),

                                Section::make('Status Tayang & Sertifikat')
                                    ->icon('heroicon-m-shield-check')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Toggle::make('is_required')
                                                    ->label('Wajib Lulus (Syarat Sertifikat)')
                                                    ->helperText('Jika aktif, murid harus lulus kuis ini untuk dapat mengklaim sertifikat kelulusan kursus.')
                                                    ->default(true),

                                                Select::make('status')
                                                    ->label('Status Publikasi')
                                                    ->options([
                                                        'draft' => 'Draft (Draf Penulis)',
                                                        'published' => 'Published (Aktif & Dapat Dikerjakan)',
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
