<?php

namespace App\Filament\Resources\Reviews\Schemas;

use App\Domain\Content\Enums\ReviewStatus;
use App\Domain\Learning\Models\Course;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Penulis & Kursus')
                ->description('Rincian murid yang memberikan penilaian dan materi kursus terkait.')
                ->schema([
                    Select::make('user_id')
                        ->label('Akun Murid / Reviewer')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpan(1),

                    Select::make('course_id')
                        ->label('Kursus yang Diulas')
                        ->relationship('course', 'title')
                        ->searchable()
                        ->preload()
                        ->placeholder('Ulasan Umum Platform TradingEdu')
                        ->columnSpan(1),

                    Select::make('rating')
                        ->label('Skor Rating Bintang')
                        ->options([
                            5 => '★★★★★ (5 Bintang - Sempurna)',
                            4 => '★★★★☆ (4 Bintang - Sangat Bagus)',
                            3 => '★★★☆☆ (3 Bintang - Cukup)',
                            2 => '★★☆☆☆ (2 Bintang - Kurang)',
                            1 => '★☆☆☆☆ (1 Bintang - Buruk)',
                        ])
                        ->default(5)
                        ->required()
                        ->columnSpan(1),

                    Select::make('status')
                        ->label('Status Moderasi')
                        ->options(ReviewStatus::options())
                        ->default(ReviewStatus::Approved->value)
                        ->required()
                        ->columnSpan(1),

                    Toggle::make('is_featured')
                        ->label('⭐ Sorot Ulasan (Featured Testimonial)')
                        ->default(false)
                        ->helperText('Ulasan unggulan akan ditampilkan di banner atas halaman detail kursus.')
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make('Ulasan & Testimoni Murid')
                ->description('Isi tanggapan objektif, kritik, atau testimoni keberhasilan trading.')
                ->schema([
                    TextInput::make('title')
                        ->label('Judul Ringkas Ulasan (Headline)')
                        ->placeholder('Contoh: Materi SMC & Order Block sangat aplikatif!')
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Textarea::make('comment')
                        ->label('Isi Tanggapan Lengkap')
                        ->required()
                        ->rows(4)
                        ->placeholder('Ceritakan pengalaman belajar, kemudahan memahami materi, atau hasil trade...')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
