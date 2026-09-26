<?php

namespace App\Filament\Resources\LiveSessions\Pages;

use App\Domain\Community\Enums\LiveSessionStatus;
use App\Domain\Community\Models\LiveSession;
use App\Filament\Resources\LiveSessions\LiveSessionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditLiveSession extends EditRecord
{
    protected static string $resource = LiveSessionResource::class;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('go_live_header')
                ->label('Mulai Live Sekarang 🔴')
                ->color('danger')
                ->icon('heroicon-m-signal')
                ->requiresConfirmation()
                ->modalHeading('Mulai Siaran Langsung (Go Live)')
                ->visible(fn (LiveSession $record): bool => $record->status === LiveSessionStatus::Upcoming)
                ->action(function (LiveSession $record) {
                    $record->update(['status' => LiveSessionStatus::Live]);
                    Notification::make()->title('Sesi Live Telah Dimulai 🔴')->success()->send();
                }),

            Action::make('complete_session_header')
                ->label('Selesaikan Sesi ✅')
                ->color('success')
                ->icon('heroicon-m-check-circle')
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

                    Notification::make()->title('Sesi telah ditandai Selesai')->success()->send();
                }),

            Action::make('open_room_header')
                ->label('Buka Ruangan Live')
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->color('primary')
                ->url(fn (LiveSession $record): string => $record->join_url)
                ->openUrlInNewTab(),

            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
