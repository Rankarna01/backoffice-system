<?php

namespace App\Domain\Community\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LiveSessionPlatform: string implements HasLabel, HasIcon
{
    case Zoom = 'zoom';
    case YouTubeLive = 'youtube_live';
    case GoogleMeet = 'google_meet';
    case Custom = 'custom';

    public function getLabel(): string
    {
        return match ($this) {
            self::Zoom => 'Zoom Meeting',
            self::YouTubeLive => 'YouTube Live',
            self::GoogleMeet => 'Google Meet',
            self::Custom => 'Custom Link / WebRTC',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Zoom => 'heroicon-m-video-camera',
            self::YouTubeLive => 'heroicon-m-play-circle',
            self::GoogleMeet => 'heroicon-m-user-group',
            self::Custom => 'heroicon-m-globe-alt',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->getLabel(),
        ])->all();
    }
}
