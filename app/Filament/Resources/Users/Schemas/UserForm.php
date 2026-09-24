<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'deleted' => 'Deleted',
                    ])
                    ->default('active')
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(50)
                    ->default(null),
                TextInput::make('timezone')
                    ->required()
                    ->default('Asia/Jakarta'),
                TextInput::make('locale')
                    ->required()
                    ->default('id'),
                DateTimePicker::make('email_verified_at')
                    ->default(now()),
                TextInput::make('uuid')
                    ->label('UUID')
                    ->default(fn () => (string) Str::uuid())
                    ->disabled()
                    ->dehydrated()
                    ->required(),
            ]);
    }
}
