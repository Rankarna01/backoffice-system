<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identitas Profil Testimoni')
                ->description('Rincian profil murid atau alumni yang memberikan testimoni.')
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1),

                    TextInput::make('role')
                        ->label('Profesi / Keterangan')
                        ->placeholder('Contoh: Full-Time Trader • Alumni Batch 3')
                        ->maxLength(255)
                        ->columnSpan(1),

                    TextInput::make('avatar_url')
                        ->label('URL Foto Profil (Avatar)')
                        ->placeholder('https://images.unsplash.com/... atau URL foto')
                        ->maxLength(500)
                        ->columnSpan(1),

                    Select::make('rating')
                        ->label('Rating Kepuasan')
                        ->options([
                            5 => '★★★★★ (5 Bintang)',
                            4 => '★★★★☆ (4 Bintang)',
                            3 => '★★★☆☆ (3 Bintang)',
                        ])
                        ->default(5)
                        ->required()
                        ->columnSpan(1),

                    TextInput::make('sort_order')
                        ->label('Urutan Tampil (Sort Order)')
                        ->numeric()
                        ->default(0)
                        ->columnSpan(1),

                    Toggle::make('is_featured')
                        ->label('⭐ Sorot di Halaman Utama (Featured)')
                        ->default(false)
                        ->columnSpan(1),

                    Toggle::make('is_published')
                        ->label('Status Publikasi Aktif')
                        ->default(true)
                        ->columnSpan(2),
                ])->columns(2),

            Section::make('Kutipan Testimoni')
                ->description('Pernyataan kepuasan, transformasi cara trading, atau pencapaian profit konsisten.')
                ->schema([
                    Textarea::make('quote')
                        ->label('Isi Kutipan Testimoni')
                        ->required()
                        ->rows(4)
                        ->placeholder('Tuliskan testimoni yang berkesan dan inspiratif...')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
