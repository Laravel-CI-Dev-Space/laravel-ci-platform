<?php

declare(strict_types=1);

namespace App\Filament\Resources\Resources;

use App\Filament\Resources\Concerns\AuthorizesViaPermission;
use App\Filament\Resources\Resources\Pages\ListResources;
use App\Filament\Resources\Resources\Tables\ResourcesTable;
use App\Models\Resource as ResourceModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ResourceResource extends Resource
{
    use AuthorizesViaPermission;

    protected static ?string $model = ResourceModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?string $navigationLabel = 'Ressources';

    protected static ?string $modelLabel = 'Ressource';

    protected static ?string $pluralModelLabel = 'Ressources';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return 'Contenu';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return ResourcesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListResources::route('/'),
        ];
    }
}
