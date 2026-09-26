<?php

namespace App\Filament\Resources\Discussions\Pages;

use App\Domain\Community\Enums\DiscussionStatus;
use App\Domain\Community\Enums\ModerationStatus;
use App\Domain\Community\Models\Discussion;
use App\Filament\Resources\Discussions\DiscussionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditDiscussion extends EditRecord
{
    protected static string $resource = DiscussionResource::class;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve_thread')
                ->label('Setujui Moderasi')
                ->color('success')
                ->icon('bx-check-circle')
                ->visible(fn (Discussion $record): bool => $record->moderation_status !== ModerationStatus::Approved)
                ->action(function (Discussion $record) {
                    $record->update([
                        'moderation_status' => ModerationStatus::Approved,
                        'status' => DiscussionStatus::Published,
                        'reports_count' => 0,
                    ]);
                    Notification::make()->title('Diskusi berhasil disetujui')->success()->send();
                }),

            Action::make('lock_thread')
                ->label(fn (Discussion $record) => $record->is_locked ? 'Buka Kunci Thread' : 'Kunci Thread')
                ->color('warning')
                ->icon(fn (Discussion $record) => $record->is_locked ? 'bx-lock-open' : 'bxs-lock')
                ->action(function (Discussion $record) {
                    $record->update(['is_locked' => ! $record->is_locked]);
                    Notification::make()->title($record->is_locked ? 'Thread telah dikunci' : 'Kunci thread dibuka')->info()->send();
                }),

            DeleteAction::make()->icon('bx-trash'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
