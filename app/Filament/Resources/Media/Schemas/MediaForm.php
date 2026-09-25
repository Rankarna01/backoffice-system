<?php

namespace App\Filament\Resources\Media\Schemas;

use App\Domain\Media\Models\MediaAsset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class MediaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Media Details')
                    ->tabs([
                        // Tab 1: Upload & Penempatan Cloudflare R2
                        Tab::make('Upload & Penempatan R2')
                            ->icon('heroicon-m-cloud-arrow-up')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Judul / Label Aset')
                                            ->placeholder('Contoh: Cover Kursus Smart Money Concepts HD')
                                            ->required()
                                            ->maxLength(255)
                                            ->helperText('Nama deskriptif untuk mempermudah pencarian media di katalog.'),

                                        Select::make('collection')
                                            ->label('Kategori / Folder Penyimpanan')
                                            ->options([
                                                'courses' => 'Cover & Thumbnail Kursus (courses/thumbnails)',
                                                'lessons' => 'Video Materi Pelajaran (lessons/videos)',
                                                'modules' => 'Dokumen Silabus & Panduan (modules/documents)',
                                                'mentors' => 'Foto Mentor & Trainer (mentors/avatars)',
                                                'marketing' => 'Banner & Grafis Promosi (marketing/banners)',
                                                'general' => 'Umum / Lampiran Bebas (general)',
                                            ])
                                            ->default('courses')
                                            ->required()
                                            ->helperText('Menentukan struktur folder direktori penyimpanan di Cloudflare R2.'),
                                    ]),

                                FileUpload::make('file_upload')
                                    ->label('Pilih File Aset (Video, Gambar, PDF, Dokumen)')
                                    ->disk('public')
                                    ->directory('r2-mock/uploads')
                                    ->maxSize(204800) // 200 MB
                                    ->acceptedFileTypes([
                                        'image/*',
                                        'video/mp4',
                                        'video/webm',
                                        'video/quicktime',
                                        'application/pdf',
                                        'application/vnd.ms-powerpoint',
                                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                        'application/vnd.ms-excel',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        'text/csv',
                                        'audio/mpeg',
                                    ])
                                    ->helperText('Mendukung video MP4/WebM, gambar WebP/PNG/JPG, dokumen PDF/PPTX/XLSX, dan Audio. Maksimal 200MB.')
                                    ->columnSpanFull()
                                    ->visibleOn('create')
                                    ->required(fn ($operation) => $operation === 'create'),

                                Section::make('Informasi File yang Tersimpan')
                                    ->visibleOn('edit')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('file_name')
                                                    ->label('Nama File Fisik')
                                                    ->disabled(),

                                                TextInput::make('mime_type')
                                                    ->label('Tipe Konten (MIME)')
                                                    ->disabled(),

                                                TextInput::make('size_bytes')
                                                    ->label('Ukuran File')
                                                    ->formatStateUsing(fn ($state, ?MediaAsset $record) => $record ? $record->size_formatted : '0 B')
                                                    ->disabled(),
                                            ]),
                                    ]),
                            ]),

                        // Tab 2: Informasi Detail & SEO
                        Tab::make('Informasi & Metadata')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                TextInput::make('alt_text')
                                    ->label('Alt Text (Aksesibilitas / SEO Gambar)')
                                    ->placeholder('Deskripsi singkat visual gambar...')
                                    ->maxLength(255)
                                    ->helperText('Teks alternatif untuk pembaca layar (screen reader) dan optimasi SEO.'),

                                Textarea::make('description')
                                    ->label('Deskripsi / Catatan Tambahan')
                                    ->placeholder('Tuliskan catatan teknis resolusi, bit-rate, atau kegunaan aset ini...')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ]),

                        // Tab 3: Cloudflare R2 Direct URLs
                        Tab::make('Cloudflare R2 CDN Links')
                            ->icon('heroicon-m-globe-alt')
                            ->visibleOn('edit')
                            ->schema([
                                TextInput::make('file_path')
                                    ->label('Path Object di Bucket R2')
                                    ->disabled()
                                    ->helperText('Alamat relatif file di dalam bucket Cloudflare R2.'),

                                TextInput::make('public_url')
                                    ->label('URL CDN Publik Edge')
                                    ->disabled()
                                    ->helperText('Tautan langsung CDN Cloudflare dengan Zero Egress Fee yang siap disematkan ke frontend.'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
