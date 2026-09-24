<?php

namespace App\Filament\Resources\Modules\Schemas;

use App\Domain\Learning\Models\Course;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ModuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Modul / Bab Pembelajaran')
                    ->description('Tentukan kursus, judul bab materi, dan ringkasan capaian belajar.')
                    ->icon('heroicon-m-rectangle-group')
                    ->schema([
                        Select::make('course_id')
                            ->label('Kursus Induk')
                            ->relationship('course', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Pilih kursus tempat modul atau bab ini bernaung.'),

                        TextInput::make('title')
                            ->label('Judul Modul / Bab')
                            ->placeholder('Contoh: Bab 1: Fondasi & Logika Pasar Smart Money')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Gunakan judul yang jelas dan terstruktur untuk memudahkan alur belajar peserta.'),

                        Textarea::make('description')
                            ->label('Tujuan & Ringkasan Bab')
                            ->placeholder('Tuliskan ringkasan topik yang akan dipelajari peserta pada bab ini...')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Deskripsi singkat modul yang ditampilkan pada silabus kursus.'),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('sort_order')
                                    ->label('Urutan Bab (Sort Order)')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->helperText('Urutan penampilan bab dalam daftar modul kursus (dimulai dari 1).'),

                                Toggle::make('is_published')
                                    ->label('Publikasikan Modul')
                                    ->default(true)
                                    ->inline(false)
                                    ->helperText('Aktifkan agar bab dan materi di dalamnya dapat diakses oleh peserta kursus.'),
                            ]),
                    ]),
            ]);
    }
}
