<?php

namespace App\Filament\Resources\Discussions\RelationManagers;

use App\Domain\Community\Models\DiscussionReply;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class RepliesRelationManager extends RelationManager
{
    protected static string $relationship = 'replies';

    protected static ?string $title = 'Balasan & Komentar Member';

    protected static ?string $modelLabel = 'Balasan';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Textarea::make('content')
                ->label('Isi Komentar / Tanggapan')
                ->required()
                ->rows(3)
                ->columnSpanFull(),

            Toggle::make('is_solution')
                ->label('Jawaban Terbaik / Solusi Mentor')
                ->default(false),

            Toggle::make('is_hidden')
                ->label('Sembunyikan dari Publik')
                ->default(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content')
            ->defaultSort('created_at', 'asc')
            ->columns([
                TextColumn::make('author.name')
                    ->label('Penulis Komentar')
                    ->weight('semibold')
                    ->description(fn (DiscussionReply $record): ?string => $record->author?->email),

                TextColumn::make('content')
                    ->label('Tanggapan')
                    ->wrap()
                    ->grow(true),

                TextColumn::make('likes_count')
                    ->label('Suka')
                    ->alignCenter(),

                IconColumn::make('is_solution')
                    ->label('Solusi')
                    ->boolean()
                    ->alignCenter(),

                ToggleColumn::make('is_hidden')
                    ->label('Disembunyikan'),

                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Beri Tanggapan Resmi')
                    ->icon('bx-comment-add')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        return $data;
                    }),
            ])
            ->recordActions([
                Action::make('toggle_solution')
                    ->label(fn (DiscussionReply $record) => $record->is_solution ? 'Batal Solusi' : 'Tandai Solusi')
                    ->icon('bxs-badge-check')
                    ->color('success')
                    ->action(function (DiscussionReply $record) {
                        $record->update(['is_solution' => ! $record->is_solution]);
                        Notification::make()
                            ->title($record->is_solution ? 'Ditandai sebagai Jawaban Terbaik!' : 'Tanda Solusi Dibatalkan')
                            ->success()
                            ->send();
                    }),
                EditAction::make()->icon('bx-edit'),
                DeleteAction::make()->icon('bx-trash'),
            ]);
    }
}
