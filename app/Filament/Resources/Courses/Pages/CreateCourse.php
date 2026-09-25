<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateCourse extends CreateRecord
{
    protected static string $resource = CourseResource::class;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }
}
