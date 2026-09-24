<?php

namespace App\Filament\Resources\MentorProfiles\Tables;

use App\Domain\Identity\Models\MentorProfile;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MentorProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Mentor')
                    ->description(fn (MentorProfile $record): ?string => $record->user?->email)
                    ->searchable(['name', 'email'])
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('headline')
                    ->label('Headline Profesional')
                    ->searchable()
                    ->limit(45)
                    ->tooltip(fn (?string $state): ?string => $state)
                    ->wrap(),

                TextColumn::make('expertise')
                    ->label('Keahlian Trading')
                    ->badge()
                    ->separator(',')
                    ->limitList(3)
                    ->color('info'),

                ToggleColumn::make('is_featured')
                    ->label('Featured')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                TernaryFilter::make('is_featured')
                    ->label('Status Featured')
                    ->placeholder('Semua Mentor')
                    ->trueLabel('Hanya Mentor Unggulan')
                    ->falseLabel('Bukan Mentor Unggulan'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
