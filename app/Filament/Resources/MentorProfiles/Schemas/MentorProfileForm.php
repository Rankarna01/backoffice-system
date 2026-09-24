<?php

namespace App\Filament\Resources\MentorProfiles\Schemas;

use App\Domain\Identity\Models\MentorProfile;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MentorProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun Mentor')
                    ->description('Pilih akun pengguna yang terdaftar sebagai mentor dan atur identitas profesionalnya.')
                    ->schema([
                        Select::make('user_id')
                            ->label('Akun Pengguna')
                            ->relationship('user', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})")
                            ->searchable(['name', 'email'])
                            ->preload()
                            ->required()
                            ->unique(MentorProfile::class, 'user_id', ignoreRecord: true)
                            ->helperText('Pilih akun pengguna yang terdaftar. Akun ini harus memiliki role mentor.')
                            ->columnSpanFull(),

                        TextInput::make('headline')
                            ->label('Headline Profesional')
                            ->placeholder('Contoh: Certified Financial Technician & Full-Time Price Action Trader')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->helperText('Gelar atau julukan singkat yang tampil di bawah nama mentor.'),

                        Textarea::make('bio')
                            ->label('Biografi & Rekam Jejak')
                            ->placeholder('Tuliskan rekam jejak, spesialisasi analisa pasar, dan filosofi trading mentor...')
                            ->rows(4)
                            ->maxLength(1500)
                            ->columnSpanFull(),
                    ]),

                Section::make('Keahlian & Pengaturan Visibilitas')
                    ->description('Tentukan tag keahlian trading serta urutan penampilan di katalog publik.')
                    ->schema([
                        TagsInput::make('expertise')
                            ->label('Spesialisasi Trading')
                            ->placeholder('Ketik keahlian lalu tekan Enter')
                            ->suggestions([
                                'Price Action',
                                'Smart Money Concepts (SMC)',
                                'Forex Major Pairs',
                                'Gold (XAUUSD)',
                                'Cryptocurrency Derivatives',
                                'Indeks Saham Global',
                                'Risk Management',
                                'Scalping & Intraday',
                                'Order Flow & Tape Reading',
                                'Macro Economics & Fundamental',
                                'Swing Trading',
                                'Algorithmic Trading',
                            ])
                            ->helperText('Tag keahlian ini akan muncul di kartu mentor dan filter pencarian kursus.')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_featured')
                                    ->label('Mentor Unggulan (Featured)')
                                    ->helperText('Jika aktif, profil mentor akan diprioritaskan di landing page & banner.')
                                    ->default(false),

                                TextInput::make('sort_order')
                                    ->label('Urutan Tampilan')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->helperText('Nomor urutan (angka lebih kecil ditampilkan lebih atas).'),
                            ]),
                    ]),

                Section::make('Tautan Saluran Komunitas & Media Sosial')
                    ->description('Kanal resmi mentor agar member dapat bergabung ke grup analisis dan media edukasi.')
                    ->schema([
                        KeyValue::make('social_links')
                            ->label('Tautan Media Sosial')
                            ->keyLabel('Platform')
                            ->keyPlaceholder('instagram / youtube / telegram / twitter')
                            ->valueLabel('Username / Tautan URL')
                            ->valuePlaceholder('@username atau https://...')
                            ->reorderable()
                            ->helperText('Daftar saluran publik mentor (misal Instagram, YouTube, Telegram Channel).')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}
