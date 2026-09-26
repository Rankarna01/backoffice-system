<?php

namespace App\Domain\Community\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LiveSessionStatus: string implements HasLabel, HasColor, HasIcon
{
    case Upcoming = 'upcoming';
    case Live = 'live';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Upcoming => 'Akan Datang',
            self::Live => 'Sedang Live 🔴',
            self::Completed => 'Selesai (Replay Tersedia)',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Upcoming => 'info',
            self::Live => 'danger',
            self::Completed => 'gray',
            self::Cancelled => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Upcoming => 'heroicon-m-clock',
            self::Live => 'heroicon-m-signal',
            self::Completed => 'heroicon-m-check-badge',
            self::Cancelled => 'heroicon-m-x-circle',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->getLabel(),
        ])->all();
    }
}
