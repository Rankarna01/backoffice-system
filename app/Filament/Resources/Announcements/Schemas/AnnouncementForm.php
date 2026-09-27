<?php

namespace App\Filament\Resources\Announcements\Schemas;

use App\Domain\Content\Enums\AnnouncementStatus;
use App\Domain\Content\Enums\AnnouncementType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Konten & Target Pengumuman')
                ->description('Tuliskan judul siaran, sasaran member, dan isi pesan lengkap.')
                ->schema([
                    TextInput::make('title')
                        ->label('Judul Pengumuman')
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

                    Select::make('type')
                        ->label('Tipe Pengumuman')
                        ->options(AnnouncementType::options())
                        ->default(AnnouncementType::Info->value)
                        ->required()
                        ->columnSpan(1),

                    Select::make('audience')
                        ->label('Target Pembaca (Audience)')
                        ->options([
                            'all' => 'Semua Member & Pengunjung',
                            'pro' => 'Khusus Member Pro',
                            'vip' => 'Khusus Member VIP Mentorship',
                        ])
                        ->default('all')
                        ->required()
                        ->columnSpan(1),

                    Select::make('status')
                        ->label('Status Penayangan')
                        ->options(AnnouncementStatus::options())
                        ->default(AnnouncementStatus::Published->value)
                        ->required()
                        ->columnSpan(1),

                    MarkdownEditor::make('body')
                        ->label('Isi Lengkap Pengumuman')
                        ->required()
                        ->placeholder('Tuliskan informasi penting, perubahan jadwal, atau panduan update...')
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make('Jadwal & Pengaturan Prioritas')
                ->description('Atur durasi penayangan otomatis dan status sticky pin.')
                ->schema([
                    Toggle::make('is_pinned')
                        ->label('Sematkan di Paling Atas (Sticky Pin)')
                        ->default(false)
                        ->helperText('Pengumuman akan selalu tampil di banner atas dashboard murid.')
                        ->columnSpan(2),

                    DateTimePicker::make('starts_at')
                        ->label('Mulai Tayang')
                        ->native(false)
                        ->seconds(false)
                        ->default(now()),

                    DateTimePicker::make('ends_at')
                        ->label('Batas Akhir Penayangan')
                        ->native(false)
                        ->seconds(false)
                        ->helperText('Kosongkan jika ingin terus tayang permanen.'),
                ])->columns(2),
        ]);
    }
}
