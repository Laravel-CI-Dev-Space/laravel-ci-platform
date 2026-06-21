<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings;

use App\Enums\UserPermission;
use App\Filament\Resources\Concerns\AuthorizesViaPermission;
use App\Filament\Resources\Settings\Pages\CreatePartner;
use App\Filament\Resources\Settings\Pages\EditPartner;
use App\Filament\Resources\Settings\Pages\ListPartners;
use App\Filament\Resources\Settings\Schemas\PartnerForm;
use App\Filament\Resources\Settings\Tables\PartnersTable;
use App\Models\Partner;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PartnerResource extends Resource
{
    use AuthorizesViaPermission;

    protected static function viewPermission(): string
    {
        return UserPermission::AdminSettings->value;
    }

    protected static ?string $model = Partner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationLabel = 'Partenaires';

    protected static ?string $modelLabel = 'Partenaire';

    protected static ?string $pluralModelLabel = 'Partenaires';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'Vitrine';
    }

    public static function form(Schema $schema): Schema
    {
        return PartnerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PartnersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPartners::route('/'),
            'create' => CreatePartner::route('/create'),
            'edit'   => EditPartner::route('/{record}/edit'),
        ];
    }
}
