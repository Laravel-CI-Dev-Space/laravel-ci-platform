<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings;

use App\Enums\UserPermission;
use App\Filament\Resources\Concerns\AuthorizesViaPermission;
use App\Filament\Resources\Settings\Pages\CreateCommunityValue;
use App\Filament\Resources\Settings\Pages\EditCommunityValue;
use App\Filament\Resources\Settings\Pages\ListCommunityValues;
use App\Filament\Resources\Settings\Schemas\CommunityValueForm;
use App\Filament\Resources\Settings\Tables\CommunityValuesTable;
use App\Models\CommunityValue;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CommunityValueResource extends Resource
{
    use AuthorizesViaPermission;

    protected static function viewPermission(): string
    {
        return UserPermission::AdminSettings->value;
    }

    protected static ?string $model = CommunityValue::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static ?string $navigationLabel = 'Valeurs';

    protected static ?string $modelLabel = 'Valeur';

    protected static ?string $pluralModelLabel = 'Valeurs';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return 'Vitrine';
    }

    public static function form(Schema $schema): Schema
    {
        return CommunityValueForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommunityValuesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCommunityValues::route('/'),
            'create' => CreateCommunityValue::route('/create'),
            'edit'   => EditCommunityValue::route('/{record}/edit'),
        ];
    }
}
