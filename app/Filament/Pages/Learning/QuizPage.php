<?php

namespace App\Filament\Pages\Learning;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class QuizPage extends Page
{
    protected string $view = 'filament.pages.learning.quiz-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;
    protected static \UnitEnum|string|null $navigationGroup = 'LEARNING';

    protected static ?string $navigationLabel = 'Quiz';

    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'quiz';

    public function getTitle(): string
    {
        return 'Quiz';
    }
}
