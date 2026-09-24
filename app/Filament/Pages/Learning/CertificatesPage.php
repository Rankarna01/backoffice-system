<?php

namespace App\Filament\Pages\Learning;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class CertificatesPage extends Page
{
    protected string $view = 'filament.pages.learning.certificates-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;
    protected static \UnitEnum|string|null $navigationGroup = 'LEARNING';

    protected static ?string $navigationLabel = 'Certificates';

    protected static ?int $navigationSort = 6;
    protected static ?string $slug = 'certificates';

    public function getTitle(): string
    {
        return 'Certificates';
    }
}
