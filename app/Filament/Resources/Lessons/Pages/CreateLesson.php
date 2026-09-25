<?php

namespace App\Filament\Resources\Lessons\Pages;

use App\Filament\Resources\Lessons\LessonResource;
use App\Filament\Resources\Modules\ModuleResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateLesson extends CreateRecord
{
    protected static string $resource = LessonResource::class;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    protected function getRedirectUrl(): string
    {
        $moduleId = request()->query('module_id') ?? $this->record->module_id ?? null;
        if ($moduleId) {
            return ModuleResource::getUrl('edit', ['record' => $moduleId]);
        }

        return $this->getResource()::getUrl('index');
    }
}
