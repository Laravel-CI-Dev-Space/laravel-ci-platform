<?php

declare(strict_types=1);

namespace App\Filament\Resources\Companies;

use App\Enums\UserPermission;
use App\Filament\Resources\Companies\Pages\ListCompanyRegistrations;
use App\Filament\Resources\Companies\Pages\ViewCompanyRegistration;
use App\Filament\Resources\Companies\Schemas\CompanyRegistrationInfolist;
use App\Filament\Resources\Companies\Tables\CompanyRegistrationsTable;
use App\Filament\Resources\Concerns\AuthorizesViaPermission;
use App\Models\CompanyRegistrationRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CompanyRegistrationResource extends Resource
{
    use AuthorizesViaPermission;

    protected static function managePermission(): string
    {
        return UserPermission::AdminUserManage->value;
    }

    protected static ?string $model = CompanyRegistrationRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static ?string $navigationLabel = 'Demandes';

    protected static ?string $modelLabel = 'Demande entreprise';

    protected static ?string $pluralModelLabel = 'Demandes entreprises';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Entreprises';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function infolist(Schema $schema): Schema
    {
        return CompanyRegistrationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompanyRegistrationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompanyRegistrations::route('/'),
            'view'  => ViewCompanyRegistration::route('/{record}'),
        ];
    }
}
