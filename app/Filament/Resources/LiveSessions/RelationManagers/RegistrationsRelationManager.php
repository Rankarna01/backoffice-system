<?php

namespace App\Filament\Resources\LiveSessions\RelationManagers;

use App\Domain\Community\Models\LiveSessionRegistration;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static ?string $title = 'Peserta Terdaftar & Presensi Kehadiran';

    protected static ?string $modelLabel = 'Peserta';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->label('Pengguna / Member')
                ->options(fn () => User::query()->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->required()
                ->disabledOn('edit'),

            Toggle::make('attended')
                ->label('Hadir pada Sesi Live')
                ->default(false),

            DateTimePicker::make('attended_at')
                ->label('Waktu Konfirmasi Kehadiran')
                ->native(false)
                ->seconds(false),

            Textarea::make('notes')
                ->label('Catatan Khusus Peserta')
                ->placeholder('Pertanyaan awal trader atau catatan kehadiran...')
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('registered_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Peserta')
                    ->weight('semibold')
                    ->description(fn (LiveSessionRegistration $record): ?string => $record->user?->email)
                    ->searchable(['name', 'email'])
                    ->sortable(),

                TextColumn::make('registered_at')
                    ->label('Waktu Pendaftaran')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                ToggleColumn::make('attended')
                    ->label('Presensi Hadir')
                    ->afterStateUpdated(function (LiveSessionRegistration $record, bool $state) {
                        $record->update([
                            'attended' => $state,
                            'attended_at' => $state ? now() : null,
                        ]);
                    })
                    ->sortable(),

                TextColumn::make('attended_at')
                    ->label('Waktu Hadir')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Belum hadir')
                    ->color('gray'),

                TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(28)
                    ->placeholder('—'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Daftarkan Peserta')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['registered_at'] = now();
                        if (! empty($data['attended']) && empty($data['attended_at'])) {
                            $data['attended_at'] = now();
                        }
                        return $data;
                    }),
            ])
            ->recordActions([
                Action::make('toggle_attendance')
                    ->label(fn (LiveSessionRegistration $record): string => $record->attended ? 'Batalkan Presensi' : 'Tandai Hadir ✅')
                    ->icon('heroicon-m-check-badge')
                    ->color(fn (LiveSessionRegistration $record): string => $record->attended ? 'gray' : 'success')
                    ->action(function (LiveSessionRegistration $record) {
                        $newStatus = ! $record->attended;
                        $record->update([
                            'attended' => $newStatus,
                            'attended_at' => $newStatus ? now() : null,
                        ]);

                        Notification::make()
                            ->title($newStatus ? 'Peserta ditandai hadir' : 'Presensi kehadiran dibatalkan')
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
