<?php

namespace App\Filament\Resources\Certificates;

use App\Domain\Learning\Models\Certificate;
use App\Filament\Resources\Certificates\Pages\ListCertificates;
use App\Filament\Resources\Certificates\Tables\CertificatesTable;
use App\Filament\Resources\Certificates\Widgets\CertificateStatsWidget;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CertificateResource extends Resource
{
    protected static ?string $model = Certificate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static \UnitEnum|string|null $navigationGroup = 'LEARNING';

    protected static ?string $navigationLabel = 'Certificates';

    protected static ?string $modelLabel = 'Sertifikat Kelulusan';

    protected static ?string $pluralModelLabel = 'Certificates';

    protected static ?string $recordTitleAttribute = 'certificate_number';

    protected static ?int $navigationSort = 6;

    public static function getNavigationBadge(): ?string
    {
        $count = Certificate::count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function table(Table $table): Table
    {
        return CertificatesTable::configure($table);
    }

    public static function getWidgets(): array
    {
        return [
            CertificateStatsWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCertificates::route('/'),
        ];
    }
}
