<?php

namespace App\Filament\Resources\Packages\Schemas;

use App\Domain\Learning\Models\Course;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identitas & Tingkatan Paket')
                ->description('Tentukan nama paket, tier (Free, Standard, Pro), dan badge sorotan untuk menarik minat member.')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('name')
                            ->label('Nama Paket')
                            ->placeholder('Contoh: Paket Standard Trader')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, callable $set) {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->columnSpan(2),

                        Select::make('tier')
                            ->label('Tingkatan (Tier)')
                            ->options([
                                'free' => 'Free (Gratis / Starter)',
                                'standard' => 'Standard (Reguler / Akses Penuh)',
                                'pro' => 'Pro (VIP All-Access / Mentorship)',
                            ])
                            ->default('standard')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state === 'free') {
                                    $set('price', 0);
                                    $set('compare_at_price', null);
                                    $set('badge', 'Starter Free');
                                    $set('includes_all_courses', false);
                                    $set('ai_monthly_quota', 20);
                                } elseif ($state === 'standard') {
                                    $set('price', 199000);
                                    $set('compare_at_price', 399000);
                                    $set('badge', 'Paling Populer');
                                    $set('includes_all_courses', false);
                                    $set('ai_monthly_quota', 100);
                                } elseif ($state === 'pro') {
                                    $set('price', 499000);
                                    $set('compare_at_price', 999000);
                                    $set('badge', 'VIP Recommended');
                                    $set('includes_all_courses', true);
                                    $set('ai_monthly_quota', 500);
                                }
                            })
                            ->columnSpan(1),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('slug')
                            ->label('URL Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('contoh: standard-trader-pack')
                            ->columnSpan(1),

                        TextInput::make('badge')
                            ->label('Badge / Label Sorotan')
                            ->placeholder('Contoh: Paling Populer, Hemat 50%, Starter')
                            ->maxLength(50)
                            ->columnSpan(1),
                    ]),

                    Textarea::make('description')
                        ->label('Deskripsi Singkat Paket')
                        ->placeholder('Tuliskan ringkasan untuk siapa paket ini cocok dan apa saja manfaat terbesarnya...')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

            Section::make('Penetapan Harga & Durasi')
                ->description('Atur skema penagihan, harga jual normal, diskon harga coret, serta batas durasi masa aktif.')
                ->schema([
                    Grid::make(4)->schema([
                        Select::make('type')
                            ->label('Tipe Durasi')
                            ->options([
                                'duration' => 'Berdasarkan Durasi (Hari)',
                                'lifetime' => 'Akses Seumur Hidup (Lifetime)',
                            ])
                            ->default('duration')
                            ->required()
                            ->live()
                            ->columnSpan(1),

                        TextInput::make('duration_days')
                            ->label('Durasi Akses (Hari)')
                            ->numeric()
                            ->default(30)
                            ->placeholder('30 = 1 Bulan, 365 = 1 Tahun')
                            ->visible(fn ($get) => $get('type') === 'duration')
                            ->required(fn ($get) => $get('type') === 'duration')
                            ->columnSpan(1),

                        TextInput::make('price')
                            ->label('Harga Jual (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->required()
                            ->helperText('Isi 0 jika paket ini gratis.')
                            ->columnSpan(1),

                        TextInput::make('compare_at_price')
                            ->label('Harga Coret (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('Contoh: 499000')
                            ->helperText('Tampilkan nominal harga awal untuk efek promo diskon.')
                            ->columnSpan(1),
                    ]),
                ]),

            Section::make('Cakupan Materi & Hak Akses Fitur')
                ->description('Pilih apakah paket membuka semua kursus atau kursus tertentu, akses sinyal, dan fitur eksklusif.')
                ->schema([
                    Grid::make(3)->schema([
                        Toggle::make('includes_all_courses')
                            ->label('Akses Seluruh Kursus (All-Access Pass)')
                            ->helperText('Jika aktif, member otomatis dapat membuka semua kursus baru maupun lama.')
                            ->default(false)
                            ->live()
                            ->columnSpan(1),

                        Toggle::make('includes_signals')
                            ->label('Akses Sinyal Trading Harian')
                            ->helperText('Izinkan member membuka feed sinyal forex, gold & kripto.')
                            ->default(true)
                            ->columnSpan(1),

                        TextInput::make('ai_monthly_quota')
                            ->label('Kuota Chat AI Assistant / Bulan')
                            ->numeric()
                            ->default(50)
                            ->columnSpan(1),
                    ]),

                    Select::make('courses')
                        ->label('Kursus Spesifik yang Termasuk')
                        ->relationship('courses', 'title')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->visible(fn ($get) => ! $get('includes_all_courses'))
                        ->helperText('Pilih satu atau beberapa kursus yang otomatis di-enroll saat member membeli paket ini.')
                        ->columnSpanFull(),

                    TagsInput::make('features')
                        ->label('Poin-Poin Fitur Unggulan')
                        ->placeholder('Ketik fitur lalu tekan Enter')
                        ->helperText('Poin fitur ini akan ditampilkan dalam bentuk checklist pada halaman katalog dan checkout.')
                        ->columnSpanFull(),
                ]),

            Section::make('Status Visibilitas & Prioritas')
                ->description('Kontrol keterlihatan paket di website publik dan urutan penampilannya.')
                ->schema([
                    Grid::make(2)->schema([
                        Toggle::make('is_active')
                            ->label('Status Aktif (Tampil di Web Publik)')
                            ->default(true)
                            ->columnSpan(1),

                        TextInput::make('sort_order')
                            ->label('Urutan Tampilan (Sort Order)')
                            ->numeric()
                            ->default(1)
                            ->helperText('Angka lebih kecil akan tampil lebih awal di katalog (1, 2, 3).')
                            ->columnSpan(1),
                    ]),
                ]),
        ]);
    }
}
