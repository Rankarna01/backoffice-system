<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Domain\Billing\Models\OrderItem;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Item Produk dalam Pesanan';

    protected static ?string $modelLabel = 'Item Pesanan';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Nama Produk / Kursus / Paket')
                ->required()
                ->columnSpan(2),

            Select::make('purchasable_type')
                ->label('Tipe Produk')
                ->options([
                    'course' => 'Kursus Satuan (Course)',
                    'plan' => 'Paket Langganan (Membership Plan)',
                ])
                ->default('plan')
                ->required(),

            TextInput::make('purchasable_id')
                ->label('ID Produk')
                ->numeric()
                ->required(),

            TextInput::make('unit_price')
                ->label('Harga Satuan')
                ->numeric()
                ->prefix('Rp')
                ->required(),

            TextInput::make('quantity')
                ->label('Jumlah')
                ->numeric()
                ->default(1)
                ->required(),

            TextInput::make('discount')
                ->label('Diskon Item')
                ->numeric()
                ->prefix('Rp')
                ->default(0),

            TextInput::make('total')
                ->label('Total Akhir Item')
                ->numeric()
                ->prefix('Rp')
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->weight('semibold')
                    ->searchable(),

                TextColumn::make('purchasable_type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'plan' ? 'info' : 'primary')
                    ->formatStateUsing(fn (string $state): string => $state === 'plan' ? 'Langganan' : 'Kursus'),

                TextColumn::make('unit_price')
                    ->label('Harga Satuan')
                    ->money('IDR', locale: 'id'),

                TextColumn::make('quantity')
                    ->label('Qty')
                    ->alignCenter(),

                TextColumn::make('discount')
                    ->label('Potongan')
                    ->money('IDR', locale: 'id')
                    ->color('gray'),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR', locale: 'id')
                    ->weight('bold'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Item')
                    ->icon('bx-plus'),
            ])
            ->recordActions([
                EditAction::make()->icon('bx-edit'),
                DeleteAction::make()->icon('bx-trash'),
            ]);
    }
}
