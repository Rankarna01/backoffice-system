<?php

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Tanya Jawab (FAQ)')
                ->description('Susun pertanyaan umum dan jawaban penjelasan komprehensif.')
                ->schema([
                    Select::make('category')
                        ->label('Kategori FAQ')
                        ->options([
                            'General' => 'Umum & Platform',
                            'Kursus & Materi' => 'Kursus & Pembelajaran',
                            'Sinyal & Indikator' => 'Sinyal Trading & Indikator',
                            'Langganan & Billing' => 'Langganan & Pembayaran',
                            'Komunitas' => 'Komunitas & Live Session',
                        ])
                        ->default('General')
                        ->required()
                        ->searchable()
                        ->columnSpan(1),

                    TextInput::make('sort_order')
                        ->label('Urutan Tampil (Sort Order)')
                        ->numeric()
                        ->default(0)
                        ->columnSpan(1),

                    Toggle::make('is_published')
                        ->label('Status Publikasi Aktif')
                        ->default(true)
                        ->columnSpanFull(),

                    TextInput::make('question')
                        ->label('Pertanyaan (Question)')
                        ->required()
                        ->maxLength(500)
                        ->placeholder('Contoh: Apakah pemula tanpa pengalaman bisa mengikuti materi?')
                        ->columnSpanFull(),

                    MarkdownEditor::make('answer')
                        ->label('Jawaban Penjelasan (Answer)')
                        ->required()
                        ->placeholder('Jelaskan secara jelas dan terstruktur...')
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }
}
