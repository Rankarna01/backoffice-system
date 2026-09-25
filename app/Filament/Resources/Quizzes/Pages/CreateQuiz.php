<?php

namespace App\Filament\Resources\Quizzes\Pages;

use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Quizzes\QuizResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateQuiz extends CreateRecord
{
    protected static string $resource = QuizResource::class;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    protected function getRedirectUrl(): string
    {
        $courseId = request()->query('course_id') ?? $this->record->course_id ?? null;
        if ($courseId) {
            return CourseResource::getUrl('edit', ['record' => $courseId]);
        }

        return $this->getResource()::getUrl('index');
    }
}
