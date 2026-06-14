<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings;

use App\Enums\UserPermission;
use App\Filament\Resources\Concerns\AuthorizesViaPermission;
use App\Filament\Resources\Settings\Pages\CreateSiteSetting;
use App\Filament\Resources\Settings\Pages\EditSiteSetting;
use App\Filament\Resources\Settings\Pages\ListSiteSettings;
use App\Filament\Resources\Settings\Schemas\SiteSettingForm;
use App\Filament\Resources\Settings\Tables\SiteSettingsTable;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SiteSettingResource extends Resource
{
    use AuthorizesViaPermission;

    protected static function viewPermission(): string
    {
        return UserPermission::AdminSettings->value;
    }

    protected static ?string $model = SiteSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Paramètres du site';

    protected static ?string $modelLabel = 'Paramètre';

    protected static ?string $pluralModelLabel = 'Paramètres du site';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Configuration';
    }

    public static function form(Schema $schema): Schema
    {
        return SiteSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiteSettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListSiteSettings::route('/'),
            'create' => CreateSiteSetting::route('/create'),
            'edit'   => EditSiteSetting::route('/{record}/edit'),
        ];
    }
}
