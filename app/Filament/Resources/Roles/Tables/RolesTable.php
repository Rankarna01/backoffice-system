<?php

namespace App\Filament\Resources\Roles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin' => 'warning',
                        'mentor' => 'info',
                        'customer' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper(str_replace('_', ' ', $state)))
                    ->description(fn (Role $record): ?string => match ($record->name) {
                        'super_admin' => 'Akses penuh tanpa batasan ke seluruh sistem TradingEdu',
                        'admin' => 'Staf operasional pengelolaan konten, sinyal, dan pesanan',
                        'mentor' => 'Instruktur materi kursus, pembuat sinyal, dan pemandu live session',
                        'customer' => 'Member / trader pembelajar platform',
                        default => 'Peran kustom sistem',
                    })
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('guard_name')
                    ->label('Guard')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('permissions_count')
                    ->label('Jumlah Izin')
                    ->counts('permissions')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('users_count')
                    ->label('Total Pengguna')
                    ->counts('users')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name', 'asc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->hidden(fn (Role $record): bool => in_array($record->name, ['super_admin', 'admin', 'mentor', 'customer'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function ($records) {
                            $records->filter(fn ($record) => !in_array($record->name, ['super_admin', 'admin', 'mentor', 'customer']))
                                ->each->delete();
                        }),
                ]),
            ]);
    }
}
