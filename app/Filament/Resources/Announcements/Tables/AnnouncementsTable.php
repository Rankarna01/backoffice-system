<?php

namespace App\Filament\Resources\Announcements\Tables;

use App\Domain\Content\Enums\AnnouncementStatus;
use App\Domain\Content\Enums\AnnouncementType;
use App\Domain\Content\Models\Announcement;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AnnouncementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('is_pinned', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Pengumuman')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->wrap()
                    ->grow(true)
                    ->description(fn (Announcement $record): string => Str($record->body)->limit(80)->stripTags()),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn ($state) => $state instanceof AnnouncementType ? $state->getColor() : 'info')
                    ->formatStateUsing(fn ($state) => $state instanceof AnnouncementType ? $state->getLabel() : (string) $state)
                    ->icon(fn ($state) => $state instanceof AnnouncementType ? $state->getIcon() : null)
                    ->sortable(),

                TextColumn::make('audience')
                    ->label('Target')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pro' => 'Member Pro',
                        'vip' => 'Member VIP',
                        default => 'Semua Member',
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state instanceof AnnouncementStatus ? $state->getColor() : 'gray')
                    ->formatStateUsing(fn ($state) => $state instanceof AnnouncementStatus ? $state->getLabel() : (string) $state)
                    ->sortable(),

                ToggleColumn::make('is_pinned')
                    ->label('Pin')
                    ->sortable(),

                TextColumn::make('starts_at')
                    ->label('Jadwal Mulai')
                    ->dateTime('d M Y')
                    ->sortable(),

                TextColumn::make('ends_at')
                    ->label('Berakhir')
                    ->state(fn (Announcement $record): string => $record->ends_at ? $record->ends_at->format('d M Y') : 'Permanen')
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(AnnouncementStatus::options()),

                SelectFilter::make('type')
                    ->label('Tipe Pengumuman')
                    ->options(AnnouncementType::options()),

                SelectFilter::make('audience')
                    ->label('Target Pembaca')
                    ->options([
                        'all' => 'Semua Member',
                        'pro' => 'Member Pro',
                        'vip' => 'Member VIP',
                    ]),

                TernaryFilter::make('is_pinned')
                    ->label('Hanya Pengumuman Pinned'),
            ])
            ->recordActions([
                EditAction::make()->icon('bx-edit'),
                DeleteAction::make()->icon('bx-trash'),
            ]);
    }
}
