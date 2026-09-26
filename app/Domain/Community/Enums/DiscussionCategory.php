<?php

namespace App\Domain\Community\Enums;

enum DiscussionCategory: string
{
    case General = 'general';
    case TechnicalAnalysis = 'technical_analysis';
    case Forex = 'forex';
    case Crypto = 'crypto';
    case Psychology = 'psychology';
    case Qna = 'qna';

    public function getLabel(): string
    {
        return match ($this) {
            self::General => 'Umum & Diskusi Bebas',
            self::TechnicalAnalysis => 'Analisis Teknikal & Setup',
            self::Forex => 'Forex & Komoditas (XAUUSD)',
            self::Crypto => 'Crypto & Blockchain',
            self::Psychology => 'Psikologi & Manajemen Risiko',
            self::Qna => 'Tanya Jawab Mentor (Q&A)',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::General => 'gray',
            self::TechnicalAnalysis => 'primary',
            self::Forex => 'warning',
            self::Crypto => 'info',
            self::Psychology => 'purple',
            self::Qna => 'success',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->getLabel(),
        ])->all();
    }
}
