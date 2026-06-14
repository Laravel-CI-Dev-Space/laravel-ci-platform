<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings;

use App\Enums\UserPermission;
use App\Filament\Resources\Concerns\AuthorizesViaPermission;
use App\Filament\Resources\Settings\Pages\CreateTeamMember;
use App\Filament\Resources\Settings\Pages\EditTeamMember;
use App\Filament\Resources\Settings\Pages\ListTeamMembers;
use App\Filament\Resources\Settings\Schemas\TeamMemberForm;
use App\Filament\Resources\Settings\Tables\TeamMembersTable;
use App\Models\TeamMember;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TeamMemberResource extends Resource
{
    use AuthorizesViaPermission;

    protected static function viewPermission(): string
    {
        return UserPermission::AdminSettings->value;
    }

    protected static ?string $model = TeamMember::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Équipe fondatrice';

    protected static ?string $modelLabel = 'Membre';

    protected static ?string $pluralModelLabel = 'Équipe fondatrice';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return 'Configuration';
    }

    public static function form(Schema $schema): Schema
    {
        return TeamMemberForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeamMembersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTeamMembers::route('/'),
            'create' => CreateTeamMember::route('/create'),
            'edit'   => EditTeamMember::route('/{record}/edit'),
        ];
    }
}
