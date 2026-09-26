<?php

namespace App\Domain\Community\Enums;

enum ModerationStatus: string
{
    case Approved = 'approved';
    case PendingReview = 'pending_review';
    case Flagged = 'flagged';
    case Rejected = 'rejected';

    public function getLabel(): string
    {
        return match ($this) {
            self::Approved => 'Disetujui (Approved)',
            self::PendingReview => 'Menunggu Moderasi',
            self::Flagged => 'Dilaporkan Member (Flagged)',
            self::Rejected => 'Ditolak / Melanggar',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Approved => 'success',
            self::PendingReview => 'warning',
            self::Flagged => 'danger',
            self::Rejected => 'gray',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->getLabel(),
        ])->all();
    }
}
