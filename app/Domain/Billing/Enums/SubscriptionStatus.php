<?php

namespace App\Domain\Billing\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SubscriptionStatus: string implements HasLabel, HasColor, HasIcon
{
    case Active = 'active';
    case Pending = 'pending';
    case Grace = 'grace';
    case Cancelled = 'cancelled';
    case Ended = 'ended';
    case Refunded = 'refunded';

    public function getLabel(): string
    {
        return match ($this) {
            self::Active => 'Aktif',
            self::Pending => 'Menunggu Aktivasi',
            self::Grace => 'Masa Tenggang (Grace)',
            self::Cancelled => 'Dibatalkan',
            self::Ended => 'Kedaluwarsa (Ended)',
            self::Refunded => 'Refund',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Pending => 'warning',
            self::Grace => 'warning',
            self::Cancelled => 'gray',
            self::Ended => 'danger',
            self::Refunded => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Active => 'bx-check-circle',
            self::Pending => 'bx-time-five',
            self::Grace => 'bx-error-circle',
            self::Cancelled => 'bx-x-circle',
            self::Ended => 'bx-stop-circle',
            self::Refunded => 'bx-undo',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->getLabel(),
        ])->all();
    }
}
