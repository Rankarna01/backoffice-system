<?php

namespace App\Filament\Resources\Lessons\Pages;

use App\Filament\Resources\Lessons\LessonResource;
use App\Filament\Resources\Modules\ModuleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditLesson extends EditRecord
{
    protected static string $resource = LessonResource::class;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        if ($this->record->module_id) {
            return ModuleResource::getUrl('edit', ['record' => $this->record->module_id]);
        }

        return $this->getResource()::getUrl('index');
    }
}
