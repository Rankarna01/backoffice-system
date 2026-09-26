<?php

namespace App\Filament\Resources\Discussions\Schemas;

use App\Domain\Community\Enums\DiscussionCategory;
use App\Domain\Community\Enums\DiscussionStatus;
use App\Domain\Community\Enums\ModerationStatus;
use App\Models\User;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class DiscussionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Thread Diskusi')
                ->description('Rincian topik, kategori, dan keterkaitan dengan materi kursus.')
                ->schema([
                    TextInput::make('title')
                        ->label('Judul Diskusi')
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

                    Select::make('user_id')
                        ->label('Penulis / Pembuat Thread')
                        ->relationship('author', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->default(auth()->id())
                        ->columnSpan(1),

                    Select::make('category')
                        ->label('Kategori Forum')
                        ->options(DiscussionCategory::options())
                        ->default(DiscussionCategory::General->value)
                        ->required()
                        ->columnSpan(1),

                    Select::make('course_id')
                        ->label('Terkait Kursus (Opsional)')
                        ->relationship('course', 'title')
                        ->searchable()
                        ->preload()
                        ->placeholder('🌐 Forum Komunitas Umum (Tanpa Kursus)')
                        ->columnSpan(1),

                    MarkdownEditor::make('content')
                        ->label('Isi Postingan / Pertanyaan')
                        ->required()
                        ->placeholder('Tuliskan konteks diskusi, setup chart, atau pertanyaan Anda di sini...')
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make('Status & Moderasi Komunitas')
                ->description('Kontrol moderasi konten, penyematan (pinning), dan penguncian tanggapan.')
                ->schema([
                    Select::make('moderation_status')
                        ->label('Status Moderasi')
                        ->options(ModerationStatus::options())
                        ->default(ModerationStatus::Approved->value)
                        ->required(),

                    Select::make('status')
                        ->label('Status Ketersediaan')
                        ->options(DiscussionStatus::options())
                        ->default(DiscussionStatus::Published->value)
                        ->required(),

                    Toggle::make('is_pinned')
                        ->label('📌 Sematkan di Paling Atas (Sticky Pin)')
                        ->default(false)
                        ->helperText('Thread akan selalu muncul di urutan paling atas forum.'),

                    Toggle::make('is_locked')
                        ->label('🔒 Kunci Diskusi (Tutup Komentar)')
                        ->default(false)
                        ->helperText('Member tidak dapat menambahkan balasan baru pada thread yang terkunci.'),

                    TextInput::make('reports_count')
                        ->label('Jumlah Laporan Member (Reports)')
                        ->numeric()
                        ->default(0)
                        ->disabled(),
                ])->columns(2),
        ]);
    }
}
