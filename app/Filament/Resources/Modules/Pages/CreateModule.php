<?php

namespace App\Filament\Resources\Modules\Pages;

use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Modules\ModuleResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateModule extends CreateRecord
{
    protected static string $resource = ModuleResource::class;

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
