<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings;

use App\Enums\UserPermission;
use App\Filament\Resources\Concerns\AuthorizesViaPermission;
use App\Filament\Resources\Settings\Pages\CreateAboutOriginSection;
use App\Filament\Resources\Settings\Pages\EditAboutOriginSection;
use App\Filament\Resources\Settings\Pages\ListAboutOriginSections;
use App\Filament\Resources\Settings\Schemas\AboutOriginSectionForm;
use App\Filament\Resources\Settings\Tables\AboutOriginSectionsTable;
use App\Models\AboutOriginSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AboutOriginSectionResource extends Resource
{
    use AuthorizesViaPermission;

    protected static function viewPermission(): string
    {
        return UserPermission::AdminSettings->value;
    }

    protected static ?string $model = AboutOriginSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Notre naissance';

    protected static ?string $modelLabel = 'Section origine';

    protected static ?string $pluralModelLabel = 'Notre naissance';

    protected static ?int $navigationSort = 7;

    public static function getNavigationGroup(): ?string
    {
        return 'Configuration';
    }

    public static function form(Schema $schema): Schema
    {
        return AboutOriginSectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AboutOriginSectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListAboutOriginSections::route('/'),
            'create' => CreateAboutOriginSection::route('/create'),
            'edit'   => EditAboutOriginSection::route('/{record}/edit'),
        ];
    }
}
