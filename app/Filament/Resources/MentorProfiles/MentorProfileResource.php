<?php

namespace App\Filament\Resources\MentorProfiles;

use App\Domain\Identity\Models\MentorProfile;
use App\Filament\Resources\MentorProfiles\Pages\CreateMentorProfile;
use App\Filament\Resources\MentorProfiles\Pages\EditMentorProfile;
use App\Filament\Resources\MentorProfiles\Pages\ListMentorProfiles;
use App\Filament\Resources\MentorProfiles\Schemas\MentorProfileForm;
use App\Filament\Resources\MentorProfiles\Tables\MentorProfilesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MentorProfileResource extends Resource
{
    protected static ?string $model = MentorProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static \UnitEnum|string|null $navigationGroup = 'USER MANAGEMENT';

    protected static ?string $navigationLabel = 'Mentors';

    protected static ?string $modelLabel = 'Mentor';

    protected static ?string $pluralModelLabel = 'Mentors';

    protected static ?string $slug = 'mentors';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return (string) MentorProfile::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function form(Schema $schema): Schema
    {
        return MentorProfileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MentorProfilesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMentorProfiles::route('/'),
            'create' => CreateMentorProfile::route('/create'),
            'edit' => EditMentorProfile::route('/{record}/edit'),
        ];
    }
}
