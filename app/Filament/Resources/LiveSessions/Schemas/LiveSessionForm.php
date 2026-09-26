<?php

namespace App\Filament\Resources\LiveSessions\Schemas;

use App\Domain\Community\Enums\LiveSessionPlatform;
use App\Domain\Community\Enums\LiveSessionStatus;
use App\Domain\Community\Enums\LiveSessionTier;
use App\Domain\Identity\Models\MentorProfile;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class LiveSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Sesi Live & Webinar')
                ->description('Rincian topik live session, mentor pembicara, dan deskripsi materi.')
                ->schema([
                    TextInput::make('title')
                        ->label('Judul Sesi / Webinar')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $operation, ?string $state, callable $set) {
                            if ($operation === 'create' && filled($state)) {
                                $set('slug', Str::slug($state) . '-' . Str::random(5));
                            }
                        })
                        ->columnSpanFull(),

                    TextInput::make('slug')
                        ->label('Slug URL')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->columnSpan(1),

                    Select::make('mentor_id')
                        ->label('Mentor / Pembicara Utama')
                        ->options(function () {
                            return MentorProfile::with('user')
                                ->get()
                                ->mapWithKeys(fn ($profile) => [
                                    $profile->id => ($profile->user?->name ?? 'Mentor #' . $profile->id) . ($profile->headline ? " — {$profile->headline}" : '')
                                ]);
                        })
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpan(1),

                    Select::make('course_id')
                        ->label('Terkait Kursus Spesifik (Opsional)')
                        ->relationship('course', 'title')
                        ->searchable()
                        ->preload()
                        ->placeholder('🌐 Sesi Terbuka / Market Breakdown Bebas')
                        ->columnSpan(1),

                    TextInput::make('cover_image_url')
                        ->label('URL Banner / Cover Image')
                        ->placeholder('https://images.unsplash.com/... atau URL banner')
                        ->maxLength(500)
                        ->columnSpan(1),

                    MarkdownEditor::make('description')
                        ->label('Deskripsi & Silabus Sesi')
                        ->placeholder('Jelaskan poin-poin yang akan dibahas, checklist persiapan, dan indikator yang akan dianalisis...')
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make('Jadwal & Akses Ruangan Live')
                ->description('Konfigurasi platform live streaming, link meeting, dan kuota peserta.')
                ->schema([
                    DateTimePicker::make('scheduled_at')
                        ->label('Waktu Pelaksanaan (Mulai)')
                        ->required()
                        ->native(false)
                        ->seconds(false)
                        ->default(now()->addDays(1)->setHour(19)->setMinute(30)),

                    TextInput::make('duration_minutes')
                        ->label('Estimasi Durasi')
                        ->numeric()
                        ->required()
                        ->default(90)
                        ->suffix('Menit'),

                    Select::make('platform')
                        ->label('Platform Streaming')
                        ->options(LiveSessionPlatform::options())
                        ->default(LiveSessionPlatform::Zoom->value)
                        ->required(),

                    TextInput::make('join_url')
                        ->label('URL Ruangan / Link Meeting')
                        ->required()
                        ->url()
                        ->placeholder('https://zoom.us/j/123456789 atau link meeting')
                        ->helperText('Tombol "Gabung Sesi" akan aktif bagi member 15 menit sebelum sesi dimulai.'),

                    TextInput::make('passcode')
                        ->label('Passcode / PIN Masuk (Opsional)')
                        ->placeholder('Contoh: TRADING2026')
                        ->maxLength(50),

                    Select::make('target_tier')
                        ->label('Akses Level Member')
                        ->options(LiveSessionTier::options())
                        ->default(LiveSessionTier::All->value)
                        ->required(),

                    TextInput::make('max_participants')
                        ->label('Kapasitas Maksimal Peserta')
                        ->numeric()
                        ->default(0)
                        ->helperText('Isi 0 jika tidak ada batasan kuota pendaftaran.'),

                    Toggle::make('is_featured')
                        ->label('⭐ Tampilkan di Hero Dashboard (Featured)')
                        ->default(false)
                        ->helperText('Sesi unggulan akan disorot di widget utama halaman dashboard siswa.'),
                ])->columns(2),

            Section::make('Status & Arsip Rekaman (Replay)')
                ->description('Pengaturan status siaran langsung dan tautan video replay setelah sesi berakhir.')
                ->schema([
                    Select::make('status')
                        ->label('Status Sesi Saat Ini')
                        ->options(LiveSessionStatus::options())
                        ->default(LiveSessionStatus::Upcoming->value)
                        ->required(),

                    TextInput::make('recording_url')
                        ->label('URL Rekaman Video (Replay)')
                        ->url()
                        ->placeholder('https://www.youtube.com/watch?v=... atau Vimeo link')
                        ->helperText('Tersedia untuk ditonton ulang setelah sesi selesai.'),

                    TextInput::make('recording_duration_minutes')
                        ->label('Durasi Video Replay')
                        ->numeric()
                        ->suffix('Menit'),

                    TextInput::make('registered_count')
                        ->label('Peserta Terdaftar')
                        ->numeric()
                        ->default(0)
                        ->disabled(),

                    TextInput::make('attended_count')
                        ->label('Peserta Hadir')
                        ->numeric()
                        ->default(0)
                        ->disabled(),
                ])->columns(2),
        ]);
    }
}
