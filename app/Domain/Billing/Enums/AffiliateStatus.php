<?php

namespace App\Domain\Billing\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AffiliateStatus: string implements HasLabel, HasColor, HasIcon
{
    case Approved = 'approved';
    case Pending = 'pending';
    case Suspended = 'suspended';

    public function getLabel(): string
    {
        return match ($this) {
            self::Approved => 'Aktif / Disetujui',
            self::Pending => 'Menunggu Persetujuan',
            self::Suspended => 'Ditangguhkan',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Approved => 'success',
            self::Pending => 'warning',
            self::Suspended => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Approved => 'bx-check-shield',
            self::Pending => 'bx-hourglass',
            self::Suspended => 'bx-block',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->getLabel(),
        ])->all();
    }
}
