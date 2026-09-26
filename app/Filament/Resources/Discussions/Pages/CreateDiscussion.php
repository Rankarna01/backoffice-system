<?php

namespace App\Filament\Resources\Discussions\Pages;

use App\Filament\Resources\Discussions\DiscussionResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateDiscussion extends CreateRecord
{
    protected static string $resource = DiscussionResource::class;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
