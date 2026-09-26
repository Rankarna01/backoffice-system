<?php

namespace App\Domain\Community\Enums;

enum DiscussionStatus: string
{
    case Published = 'published';
    case Pinned = 'pinned';
    case Hidden = 'hidden';
    case Locked = 'locked';
    case Flagged = 'flagged';

    public function getLabel(): string
    {
        return match ($this) {
            self::Published => 'Publikasi',
            self::Pinned => 'Disematkan (Pinned)',
            self::Hidden => 'Disembunyikan (Hidden)',
            self::Locked => 'Terkunci (Locked)',
            self::Flagged => 'Ditandai / Dilaporkan',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Published => 'success',
            self::Pinned => 'warning',
            self::Hidden => 'gray',
            self::Locked => 'danger',
            self::Flagged => 'danger',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->getLabel(),
        ])->all();
    }
}
