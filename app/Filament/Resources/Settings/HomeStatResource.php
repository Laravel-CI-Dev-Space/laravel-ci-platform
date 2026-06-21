<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings;

use App\Enums\UserPermission;
use App\Filament\Resources\Concerns\AuthorizesViaPermission;
use App\Filament\Resources\Settings\Pages\CreateHomeStat;
use App\Filament\Resources\Settings\Pages\EditHomeStat;
use App\Filament\Resources\Settings\Pages\ListHomeStats;
use App\Filament\Resources\Settings\Schemas\HomeStatForm;
use App\Filament\Resources\Settings\Tables\HomeStatsTable;
use App\Models\HomeStat;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HomeStatResource extends Resource
{
    use AuthorizesViaPermission;

    protected static function viewPermission(): string
    {
        return UserPermission::AdminSettings->value;
    }

    protected static ?string $model = HomeStat::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Stats page accueil';

    protected static ?string $modelLabel = 'Statistique';

    protected static ?string $pluralModelLabel = 'Stats page accueil';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Vitrine';
    }

    public static function form(Schema $schema): Schema
    {
        return HomeStatForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomeStatsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListHomeStats::route('/'),
            'create' => CreateHomeStat::route('/create'),
            'edit'   => EditHomeStat::route('/{record}/edit'),
        ];
    }
}
