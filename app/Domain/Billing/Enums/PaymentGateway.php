<?php

namespace App\Domain\Billing\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentGateway: string implements HasLabel, HasIcon
{
    case Midtrans = 'midtrans';
    case Xendit = 'xendit';
    case Manual = 'manual';

    public function getLabel(): string
    {
        return match ($this) {
            self::Midtrans => 'Midtrans Payment Gateway',
            self::Xendit => 'Xendit Payment Gateway',
            self::Manual => 'Transfer Bank Manual',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Midtrans => 'bx-credit-card',
            self::Xendit => 'bx-wallet',
            self::Manual => 'bx-transfer',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->getLabel(),
        ])->all();
    }
}
