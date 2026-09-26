<?php

namespace App\Domain\Community\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LiveSessionTier: string implements HasLabel, HasColor
{
    case All = 'all';
    case Free = 'free';
    case Pro = 'pro';
    case Vip = 'vip';

    public function getLabel(): string
    {
        return match ($this) {
            self::All => 'Semua Member (Publik)',
            self::Free => 'Free Member Minimal',
            self::Pro => 'Pro Trader Khusus',
            self::Vip => 'VIP / 1-on-1 Mentorship',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::All => 'gray',
            self::Free => 'info',
            self::Pro => 'warning',
            self::Vip => 'danger',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->getLabel(),
        ])->all();
    }
}
