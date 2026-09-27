<?php

namespace App\Domain\Billing\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasLabel, HasColor, HasIcon
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Expired = 'expired';
    case Refunded = 'refunded';
    case PartiallyRefunded = 'partially_refunded';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Pembayaran',
            self::Paid => 'Lunas / Berhasil',
            self::Failed => 'Gagal',
            self::Expired => 'Kedaluwarsa',
            self::Refunded => 'Refund (Dikembalikan)',
            self::PartiallyRefunded => 'Sebagian Refund',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Paid => 'success',
            self::Failed => 'danger',
            self::Expired => 'gray',
            self::Refunded => 'danger',
            self::PartiallyRefunded => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pending => 'bx-time',
            self::Paid => 'bx-check-double',
            self::Failed => 'bx-x',
            self::Expired => 'bx-calendar-x',
            self::Refunded => 'bx-undo',
            self::PartiallyRefunded => 'bx-redo',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->getLabel(),
        ])->all();
    }
}
