<?php

namespace App\Filament\Resources\Modules\Schemas;

use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\Module;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ModuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Module Details')
                    ->tabs([
                        // Tab 1: Informasi Dasar & Silabus
                        Tab::make('Informasi Dasar & Silabus')
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
                                            ->default(fn () => request()->query('course_id'))
                                            ->helperText('Pilih kursus induk tempat modul / bab ini bernaung.'),

                                        TextInput::make('sort_order')
                                            ->label('Urutan Bab (Sort Order)')
                                            ->numeric()
                                            ->default(function () {
                                                $courseId = request()->query('course_id');
                                                if ($courseId) {
                                                    $max = Module::where('course_id', $courseId)->max('sort_order') ?? 0;
                                                    return $max + 1;
                                                }
                                                return 1;
                                            })
                                            ->required()
                                            ->helperText('Nomor urutan bab di dalam silabus kurikulum kursus.'),
                                    ]),

                                TextInput::make('title')
                                    ->label('Judul Modul / Bab')
                                    ->placeholder('Contoh: Bab 1: Fondasi & Logika Pasar Smart Money')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Gunakan judul yang jelas dan terstruktur untuk memandu alur belajar peserta.'),

                                Textarea::make('description')
                                    ->label('Tujuan & Ringkasan Silabus Bab')
                                    ->placeholder('Jelaskan materi inti, topik-topik bahasan, dan capaian pembelajaran yang dibahas pada bab ini...')
                                    ->rows(6)
                                    ->columnSpanFull()
                                    ->helperText('Deskripsi singkat modul yang ditampilkan pada silabus kursus bagi peserta.'),
                            ]),

                        // Tab 2: Pengaturan & Publikasi
                        Tab::make('Pengaturan & Publikasi')
                            ->icon('heroicon-m-cog-6-tooth')
                            ->schema([
                                Section::make('Visibilitas & Akses Peserta')
                                    ->icon('heroicon-m-eye')
                                    ->schema([
                                        Toggle::make('is_published')
                                            ->label('Publikasikan Modul / Bab Ini')
                                            ->default(true)
                                            ->inline(false)
                                            ->helperText('Jika aktif, modul dan seluruh materi pelajaran di dalamnya dapat diakses oleh peserta kursus yang terdaftar.'),
                                    ]),

                                Section::make('Panduan Struktur Kurikulum')
                                    ->icon('heroicon-m-academic-cap')
                                    ->description('Informasi hirarki pembelajaran platform TradingEdu.')
                                    ->schema([
                                        TextInput::make('curriculum_guide')
                                            ->label('Catatan Struktur Pembelajaran')
                                            ->default('Course (Kursus) → Modules (Bab) → Lessons (Pelajaran Video/Materi) & Quiz (Evaluasi)')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->helperText('Setelah bab dibuat, Anda dapat menambahkan materi pelajaran (lessons) dan evaluasi pemahaman di menu Lessons & Quiz.'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
