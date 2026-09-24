<?php

namespace App\Filament\Pages\UserManagement;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class RolesPermissionsPage extends Page
{
    protected string $view = 'filament.pages.user-management.roles-permissions-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;
    protected static \UnitEnum|string|null $navigationGroup = 'USER MANAGEMENT';

    protected static ?string $navigationLabel = 'Roles & Permissions';

    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'roles-permissions';

    public function getTitle(): string
    {
        return 'Roles & Permissions';
    }
}
