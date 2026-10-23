<?php

namespace App\Filament\Widgets;

use App\Domain\Community\Models\LiveSession;
use App\Domain\Learning\Models\Course;
use Filament\Widgets\Widget;

class UpcomingSessionsAndApprovalWidget extends Widget
{
    protected string $view = 'filament.widgets.upcoming-sessions-and-approval-widget';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = [
        'default' => 1,
        'xl' => 1,
    ];

    protected static ?int $sort = 5;

    public function getViewData(): array
    {
        $sessions = LiveSession::with('mentor.user')
            ->orderBy('scheduled_at', 'desc')
            ->take(3)
            ->get();

        $pendingCourses = Course::latest()
            ->take(2)
            ->get();

        return [
            'sessions' => $sessions,
            'pendingCourses' => $pendingCourses,
        ];
    }
}
