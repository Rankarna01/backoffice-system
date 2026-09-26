<?php

namespace App\Filament\Resources\LiveSessions\Tables;

use App\Domain\Community\Enums\LiveSessionPlatform;
use App\Domain\Community\Enums\LiveSessionStatus;
use App\Domain\Community\Enums\LiveSessionTier;
use App\Domain\Community\Models\LiveSession;
use App\Domain\Identity\Models\MentorProfile;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class LiveSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('scheduled_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul & Instruktur')
                    ->searchable(['title', 'description'])
                    ->sortable()
                    ->weight('semibold')
                    ->wrap()
                    ->grow(true)
                    ->description(function (LiveSession $record): string {
                        $mentorName = $record->mentorProfile?->user?->name ?? 'Mentor Belum Ditentukan';
                        $courseTitle = $record->course?->title ? " · Kursus: {$record->course->title}" : '';
                        return "Oleh: {$mentorName}{$courseTitle}";
                    }),

                TextColumn::make('scheduled_at')
                    ->label('Jadwal Pelaksanaan')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->description(fn (LiveSession $record): string => $record->scheduled_at?->diffForHumans() ?? ''),

                TextColumn::make('duration_minutes')
                    ->label('Durasi')
                    ->suffix(' mnt')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('platform')
                    ->label('Platform')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => $state instanceof LiveSessionPlatform ? $state->getLabel() : (string) $state)
                    ->icon(fn ($state) => $state instanceof LiveSessionPlatform ? $state->getIcon() : null),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state instanceof LiveSessionStatus ? $state->getColor() : 'gray')
                    ->formatStateUsing(fn ($state) => $state instanceof LiveSessionStatus ? $state->getLabel() : (string) $state)
                    ->icon(fn ($state) => $state instanceof LiveSessionStatus ? $state->getIcon() : null)
                    ->sortable(),

                TextColumn::make('target_tier')
                    ->label('Akses Tier')
                    ->badge()
                    ->color(fn ($state) => $state instanceof LiveSessionTier ? $state->getColor() : 'gray')
                    ->formatStateUsing(fn ($state) => $state instanceof LiveSessionTier ? $state->getLabel() : (string) $state),

                TextColumn::make('attendance')
                    ->label('Peserta Terdaftar')
                    ->state(function (LiveSession $record): string {
                        $max = $record->max_participants > 0 ? $record->max_participants : '∞';
                        return "👥 {$record->registered_count}/{$max} ({$record->attended_count} hadir)";
                    })
                    ->color('gray'),

                TextColumn::make('recording')
                    ->label('Replay')
                    ->state(fn (LiveSession $record): string => ! empty($record->recording_url) ? '🎬 Tersedia' : '—')
                    ->badge()
                    ->color(fn (string $state): string => $state === '🎬 Tersedia' ? 'success' : 'gray'),

                ToggleColumn::make('is_featured')
                    ->label('Featured ⭐')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Sesi')
                    ->options(LiveSessionStatus::options()),

                SelectFilter::make('platform')
                    ->label('Platform Streaming')
                    ->options(LiveSessionPlatform::options()),

                SelectFilter::make('target_tier')
                    ->label('Akses Level')
                    ->options(LiveSessionTier::options()),

                SelectFilter::make('mentor_id')
                    ->label('Mentor Instruktur')
                    ->options(function () {
                        return MentorProfile::with('user')
                            ->get()
                            ->mapWithKeys(fn ($profile) => [
                                $profile->id => $profile->user?->name ?? 'Mentor #' . $profile->id
                            ]);
                    }),

                TernaryFilter::make('is_featured')
                    ->label('Hanya Sesi Pilihan (Featured)'),

                TernaryFilter::make('has_recording')
                    ->label('Hanya dengan Rekaman Replay')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('recording_url')->where('recording_url', '!=', ''),
                        false: fn ($query) => $query->whereNull('recording_url')->orWhere('recording_url', ''),
                    ),
            ])
            ->recordActions([
                Action::make('go_live')
                    ->label('Mulai Live 🔴')
                    ->icon('heroicon-m-signal')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Mulai Siaran Langsung (Go Live)')
                    ->modalDescription('Apakah Anda yakin ingin memulai sesi live ini sekarang? Status akan berubah menjadi LIVE dan tombol akses meeting akan dibuka untuk peserta.')
                    ->visible(fn (LiveSession $record): bool => $record->status === LiveSessionStatus::Upcoming)
                    ->action(function (LiveSession $record) {
                        $record->update(['status' => LiveSessionStatus::Live]);

                        Notification::make()
                            ->title('Sesi Live Telah Dimulai! 🔴')
                            ->body("Sesi '{$record->title}' kini berstatus Live dan dapat diakses peserta.")
                            ->success()
                            ->send();
                    }),

                Action::make('complete_session')
                    ->label('Selesaikan Sesi')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn (LiveSession $record): bool => $record->status === LiveSessionStatus::Live)
                    ->form([
                        TextInput::make('recording_url')
                            ->label('URL Rekaman Replay (Opsional)')
                            ->url()
                            ->placeholder('https://youtube.com/watch?v=...'),
                        TextInput::make('recording_duration_minutes')
                            ->label('Durasi Rekaman (Menit)')
                            ->numeric()
                            ->suffix('Menit'),
                    ])
                    ->action(function (LiveSession $record, array $data) {
                        $updateData = ['status' => LiveSessionStatus::Completed];
                        if (! empty($data['recording_url'])) {
                            $updateData['recording_url'] = $data['recording_url'];
                        }
                        if (! empty($data['recording_duration_minutes'])) {
                            $updateData['recording_duration_minutes'] = $data['recording_duration_minutes'];
                        }
                        $record->update($updateData);

                        Notification::make()
                            ->title('Sesi Berhasil Diselesaikan')
                            ->body('Sesi telah ditandai selesai dan rekaman replay siap dipublikasikan.')
                            ->success()
                            ->send();
                    }),

                Action::make('open_room')
                    ->label('Buka Ruangan')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('primary')
                    ->url(fn (LiveSession $record): string => $record->join_url)
                    ->openUrlInNewTab(),

                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
