<?php

namespace App\Filament\Pages;

use App\Domain\Notifications\Models\BroadcastNotification;
use App\Domain\Notifications\Models\NotificationLog;
use App\Filament\Widgets\NotificationStatsWidget;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NotificationsPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.notifications-page';

    protected static string|BackedEnum|null $navigationIcon = 'bx-bell';

    protected static ?string $navigationLabel = 'Notifications';

    protected static ?int $navigationSort = 80;

    protected static ?string $slug = 'notifications';

    public function getTitle(): string
    {
        return 'Pusat Notifikasi & Siaran (Broadcast)';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            NotificationStatsWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_broadcast')
                ->label('Buat Notifikasi Siaran')
                ->icon('heroicon-m-plus-circle')
                ->color('primary')
                ->button()
                ->modalHeading('Kirim Notifikasi Siaran Baru')
                ->modalDescription('Siarkan pesan pemberitahuan ke aplikasi mobile dan web member secara terpadu.')
                ->modalWidth('2xl')
                ->form([
                    TextInput::make('title')
                        ->label('Judul Notifikasi')
                        ->placeholder('Contoh: Webinar Eksklusif: Analisis NFP Malam Ini')
                        ->required()
                        ->maxLength(255),

                    Textarea::make('body')
                        ->label('Isi Pesan Notifikasi')
                        ->placeholder('Tuliskan rincian pengumuman atau instruksi...')
                        ->required()
                        ->rows(4),

                    Select::make('type')
                        ->label('Kategori / Tipe Pesan')
                        ->options([
                            'announcement' => 'Pengumuman Resmi (Announcement)',
                            'webinar' => 'Webinar / Live Session',
                            'signal' => 'Sinyal Pasar & Setup Trading',
                            'promo' => 'Promo & Diskon Eksklusif',
                            'system' => 'Pembaruan Sistem / Maintenance',
                        ])
                        ->default('announcement')
                        ->required(),

                    Select::make('target_audience')
                        ->label('Target Audiens Penerima')
                        ->options([
                            'all' => 'Semua Pengguna Terdaftar (All Users)',
                            'subscribers' => 'Pelanggan Pro / VIP Subscribers',
                            'free_members' => 'Pengguna Akun Gratis (Free Members)',
                        ])
                        ->default('all')
                        ->required(),

                    TextInput::make('action_url')
                        ->label('Tautan Aksi (Action URL / Deep Link)')
                        ->placeholder('Contoh: /market/signals atau /learning/courses')
                        ->helperText('Jika diisi, member dapat mengklik notifikasi untuk langsung membuka halaman ini.'),

                    CheckboxList::make('channels')
                        ->label('Saluran Notifikasi')
                        ->options([
                            'in_app' => 'Notifikasi Dalam Aplikasi (In-App)',
                            'email' => 'Surat Elektronik (Email Blast)',
                            'push' => 'Push Notification Peramban / HP',
                        ])
                        ->default(['in_app'])
                        ->required(),

                    Toggle::make('send_now')
                        ->label('Kirim Seketika (Broadcast Sekarang)')
                        ->default(true)
                        ->reactive(),

                    DateTimePicker::make('scheduled_at')
                        ->label('Waktu Penjadwalan Siaran')
                        ->native(false)
                        ->visible(fn ($get) => ! $get('send_now'))
                        ->required(fn ($get) => ! $get('send_now')),
                ])
                ->action(function (array $data) {
                    $sendNow = (bool) ($data['send_now'] ?? true);
                    $status = $sendNow ? 'sent' : 'scheduled';
                    $sentAt = $sendNow ? now() : null;
                    $scheduledAt = ! $sendNow ? $data['scheduled_at'] : null;

                    $broadcast = BroadcastNotification::create([
                        'title' => $data['title'],
                        'body' => $data['body'],
                        'type' => $data['type'],
                        'target_audience' => $data['target_audience'],
                        'action_url' => $data['action_url'] ?: null,
                        'channels' => $data['channels'] ?: ['in_app'],
                        'status' => $status,
                        'scheduled_at' => $scheduledAt,
                        'sent_at' => $sentAt,
                        'created_by' => auth()->id(),
                    ]);

                    if ($sendNow) {
                        $this->dispatchBroadcastToAudience($broadcast);
                    }

                    Notification::make()
                        ->title($sendNow ? 'Notifikasi Berhasil Disiarkan!' : 'Jadwal Siaran Disimpan!')
                        ->body($sendNow ? 'Pesan telah didistribusikan ke target audiens member.' : 'Pesan akan terkirim otomatis pada waktu yang ditentukan.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                BroadcastNotification::query()->latest('created_at')
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Judul & Isi Notifikasi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (BroadcastNotification $record): string => Str::limit($record->body, 70))
                    ->wrap(),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'signal' => 'success',
                        'webinar' => 'info',
                        'promo' => 'warning',
                        'system' => 'danger',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'signal' => 'Sinyal Pasar',
                        'webinar' => 'Webinar / Live',
                        'promo' => 'Promo & Diskon',
                        'system' => 'Pembaruan',
                        default => 'Pengumuman',
                    })
                    ->sortable(),

                TextColumn::make('target_audience')
                    ->label('Target Audiens')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'subscribers' => 'VIP Members',
                        'free_members' => 'Free Members',
                        default => 'Semua User',
                    }),

                TextColumn::make('channels')
                    ->label('Saluran')
                    ->badge()
                    ->color('info')
                    ->separator(','),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'sent' => 'success',
                        'scheduled' => 'warning',
                        'draft' => 'gray',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'sent' => 'Terkirim',
                        'scheduled' => 'Terjadwal',
                        'draft' => 'Draft',
                        default => 'Dibatalkan',
                    })
                    ->sortable(),

                TextColumn::make('recipients_and_reads')
                    ->label('Jangkauan & Baca')
                    ->state(fn (BroadcastNotification $record): string => "{$record->read_count} / {$record->total_recipients} ({$record->read_rate}%)")
                    ->color('gray'),

                TextColumn::make('sent_at')
                    ->label('Waktu Siaran')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Belum dikirim')
                    ->color('gray')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'sent' => 'Terkirim',
                        'scheduled' => 'Terjadwal',
                        'draft' => 'Draft',
                        'cancelled' => 'Dibatalkan',
                    ]),

                SelectFilter::make('type')
                    ->label('Tipe Pesan')
                    ->options([
                        'announcement' => 'Pengumuman',
                        'webinar' => 'Webinar / Live',
                        'signal' => 'Sinyal Trading',
                        'promo' => 'Promo & Diskon',
                        'system' => 'Pembaruan Sistem',
                    ]),

                SelectFilter::make('target_audience')
                    ->label('Target Audiens')
                    ->options([
                        'all' => 'Semua User',
                        'subscribers' => 'VIP Members',
                        'free_members' => 'Free Members',
                    ]),
            ])
            ->actions([
                Action::make('send_now')
                    ->label('Kirim Sekarang')
                    ->icon('heroicon-m-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pengiriman Siaran')
                    ->modalDescription('Kirim notifikasi ini sekarang ke seluruh pengguna yang ditargetkan?')
                    ->visible(fn (BroadcastNotification $record): bool => $record->status !== 'sent')
                    ->action(function (BroadcastNotification $record) {
                        $record->update([
                            'status' => 'sent',
                            'sent_at' => now(),
                        ]);

                        $this->dispatchBroadcastToAudience($record);

                        Notification::make()
                            ->title('Notifikasi Berhasil Dikirim')
                            ->body("Siaran \"{$record->title}\" telah disebarkan.")
                            ->success()
                            ->send();
                    }),

                EditAction::make()
                    ->modalHeading('Edit Notifikasi Siaran')
                    ->form([
                        TextInput::make('title')->label('Judul Notifikasi')->required(),
                        Textarea::make('body')->label('Isi Pesan Notifikasi')->required()->rows(4),
                        Select::make('type')
                            ->label('Tipe Pesan')
                            ->options([
                                'announcement' => 'Pengumuman',
                                'webinar' => 'Webinar / Live',
                                'signal' => 'Sinyal Pasar',
                                'promo' => 'Promo & Diskon',
                                'system' => 'Pembaruan',
                            ])->required(),
                        Select::make('target_audience')
                            ->label('Target Audiens')
                            ->options([
                                'all' => 'Semua User',
                                'subscribers' => 'VIP Members',
                                'free_members' => 'Free Members',
                            ])->required(),
                        TextInput::make('action_url')->label('Tautan Aksi'),
                    ]),

                DeleteAction::make(),
            ]);
    }

    protected function dispatchBroadcastToAudience(BroadcastNotification $broadcast): void
    {
        $query = User::query();

        if ($broadcast->target_audience === 'subscribers') {
            $query->whereHas('subscriptions', function ($q) {
                $q->where('status', 'active');
            });
        } elseif ($broadcast->target_audience === 'free_members') {
            $query->whereDoesntHave('subscriptions', function ($q) {
                $q->where('status', 'active');
            });
        }

        $recipients = $query->take(500)->get();

        $broadcast->update([
            'total_recipients' => $recipients->count(),
            'read_count' => 0,
        ]);

        $logs = [];
        $channels = $broadcast->channels ?: ['in_app'];

        foreach ($recipients as $recipient) {
            foreach ($channels as $channel) {
                $logs[] = [
                    'broadcast_id' => $broadcast->id,
                    'user_id' => $recipient->id,
                    'channel' => $channel,
                    'is_read' => false,
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (! empty($logs)) {
            NotificationLog::insert($logs);
        }
    }
}
