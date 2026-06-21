<?php

declare(strict_types=1);

namespace App\Filament\Resources\Companies;

use App\Enums\UserPermission;
use App\Filament\Resources\Companies\Pages\ListCompanyAccounts;
use App\Filament\Resources\Companies\Tables\CompanyAccountsTable;
use App\Filament\Resources\Concerns\AuthorizesViaPermission;
use App\Models\CompanyAccount;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CompanyAccountResource extends Resource
{
    use AuthorizesViaPermission;

    protected static function managePermission(): string
    {
        return UserPermission::AdminUserManage->value;
    }

    protected static ?string $model = CompanyAccount::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Comptes';

    protected static ?string $modelLabel = 'Compte entreprise';

    protected static ?string $pluralModelLabel = 'Comptes entreprises';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Recruteurs';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return CompanyAccountsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('company');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompanyAccounts::route('/'),
        ];
    }
}
