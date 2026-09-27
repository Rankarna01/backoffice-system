<?php

namespace App\Domain\Content\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AnnouncementType: string implements HasLabel, HasColor, HasIcon
{
    case Info = 'info';
    case Warning = 'warning';
    case Promo = 'promo';
    case Maintenance = 'maintenance';

    public function getLabel(): string
    {
        return match ($this) {
            self::Info => 'Informasi Umum',
            self::Warning => 'Peringatan Penting',
            self::Promo => 'Promo & Event',
            self::Maintenance => 'Pemeliharaan Sistem',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Info => 'info',
            self::Warning => 'warning',
            self::Promo => 'success',
            self::Maintenance => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Info => 'bx-info-circle',
            self::Warning => 'bx-error-circle',
            self::Promo => 'bx-purchase-tag',
            self::Maintenance => 'bx-wrench',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->getLabel(),
        ])->all();
    }
}
