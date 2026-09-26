<?php

namespace App\Filament\Resources\Discussions\Tables;

use App\Domain\Community\Enums\DiscussionCategory;
use App\Domain\Community\Enums\DiscussionStatus;
use App\Domain\Community\Enums\ModerationStatus;
use App\Domain\Community\Models\Discussion;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class DiscussionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('is_pinned', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Topik & Penulis')
                    ->searchable(['title', 'content'])
                    ->sortable()
                    ->weight('semibold')
                    ->wrap()
                    ->grow(true)
                    ->description(fn (Discussion $record): string => "Oleh: {$record->author?->name} ({$record->author?->email}) · {$record->created_at?->diffForHumans()}"),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn ($state) => $state instanceof DiscussionCategory ? $state->getColor() : 'gray')
                    ->formatStateUsing(fn ($state) => $state instanceof DiscussionCategory ? $state->getLabel() : (string) $state)
                    ->sortable(),

                TextColumn::make('course.title')
                    ->label('Materi Kursus')
                    ->default('Forum Umum')
                    ->badge()
                    ->color('gray')
                    ->limit(24),

                TextColumn::make('moderation_status')
                    ->label('Status Moderasi')
                    ->badge()
                    ->color(fn ($state) => $state instanceof ModerationStatus ? $state->getColor() : 'gray')
                    ->formatStateUsing(fn ($state) => $state instanceof ModerationStatus ? $state->getLabel() : (string) $state)
                    ->sortable(),

                ToggleColumn::make('is_pinned')
                    ->label('Pin')
                    ->sortable(),

                ToggleColumn::make('is_locked')
                    ->label('Kunci')
                    ->sortable(),

                TextColumn::make('engagement')
                    ->label('Interaksi')
                    ->state(fn (Discussion $record): string => "{$record->views_count} views · {$record->likes_count} likes · {$record->replies_count} balasan")
                    ->color('gray'),

                TextColumn::make('reports_count')
                    ->label('Laporan')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'gray')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori Forum')
                    ->options(DiscussionCategory::options()),

                SelectFilter::make('moderation_status')
                    ->label('Status Moderasi')
                    ->options(ModerationStatus::options()),

                TernaryFilter::make('is_pinned')
                    ->label('Hanya Disematkan (Pinned)'),

                TernaryFilter::make('is_locked')
                    ->label('Hanya Terkunci (Locked)'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('bx-check-circle')
                    ->color('success')
                    ->visible(fn (Discussion $record): bool => $record->moderation_status !== ModerationStatus::Approved)
                    ->action(function (Discussion $record) {
                        $record->update([
                            'moderation_status' => ModerationStatus::Approved,
                            'status' => DiscussionStatus::Published,
                            'reports_count' => 0,
                        ]);

                        Notification::make()
                            ->title('Diskusi Disetujui!')
                            ->body('Status thread kini aktif dan dapat diakses member.')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Tolak / Sembunyikan')
                    ->icon('bx-block')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Discussion $record): bool => $record->moderation_status !== ModerationStatus::Rejected)
                    ->action(function (Discussion $record) {
                        $record->update([
                            'moderation_status' => ModerationStatus::Rejected,
                            'status' => DiscussionStatus::Hidden,
                        ]);

                        Notification::make()
                            ->title('Diskusi Ditolak & Disembunyikan')
                            ->body('Thread ini telah diarsipkan dan tidak lagi tampil di forum publik.')
                            ->warning()
                            ->send();
                    }),

                EditAction::make()->icon('bx-edit'),
                DeleteAction::make()->icon('bx-trash'),
            ]);
    }
}
