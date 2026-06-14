<?php

namespace App\Filament\Resources\Users;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Membres';

    protected static ?string $modelLabel = 'Membre';

    protected static ?string $pluralModelLabel = 'Membres';

    protected static ?int $navigationSort = 1;

    // Defined as a method to avoid a Filament type conflict with the string|BackedEnum property
    public static function getNavigationGroup(): ?string
    {
        return 'Membres';
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can(UserPermission::AdminUserManage->value) ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can(UserPermission::AdminUserManage->value) ?? false;
    }

    /**
     * La création manuelle de comptes et la suppression sont réservées au
     * super-admin : ce sont les opérations les plus sensibles (un compte
     * supprimé/créé peut contourner le flux d'inscription GitHub).
     */
    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole(UserRole::SuperAdmin->value) ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasRole(UserRole::SuperAdmin->value) ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view'   => ViewUser::route('/{record}'),
            'edit'   => EditUser::route('/{record}/edit'),
        ];
    }

    /**
     * Super-admin sees all users. Admin cannot see super-admin accounts.
     * Eager-loads roles + profile to prevent N+1 in the table and infolist.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['roles', 'profile.grade']);

        /** @var User $authUser */
        $authUser = auth()->user();

        if (! $authUser->hasRole(UserRole::SuperAdmin->value)) {
            $query->whereDoesntHave('roles', fn ($q) => $q->where('name', UserRole::SuperAdmin->value));
        }

        return $query;
    }
}
