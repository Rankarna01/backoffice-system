<?php

namespace App\Filament\Resources\Certificates\Pages;

use App\Filament\Resources\Certificates\CertificateResource;
use App\Filament\Resources\Certificates\Widgets\CertificateStatsWidget;
use Filament\Resources\Pages\ListRecords;

class ListCertificates extends ListRecords
{
    protected static string $resource = CertificateResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            CertificateStatsWidget::class,
        ];
    }
}
