<?php

declare(strict_types=1);

namespace App\Filament\Resources\Grades;

use App\Enums\UserPermission;
use App\Filament\Resources\Concerns\AuthorizesViaPermission;
use App\Filament\Resources\Grades\Pages\CreateGrade;
use App\Filament\Resources\Grades\Pages\EditGrade;
use App\Filament\Resources\Grades\Pages\ListGrades;
use App\Filament\Resources\Grades\Schemas\GradeForm;
use App\Filament\Resources\Grades\Tables\GradesTable;
use App\Models\Grade;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GradeResource extends Resource
{
    use AuthorizesViaPermission;

    protected static function viewPermission(): string
    {
        return UserPermission::AdminSettings->value;
    }

    protected static ?string $model = Grade::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static ?string $navigationLabel = 'Grades';

    protected static ?string $modelLabel = 'Grade';

    protected static ?string $pluralModelLabel = 'Grades';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'Membres';
    }

    public static function form(Schema $schema): Schema
    {
        return GradeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GradesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListGrades::route('/'),
            'create' => CreateGrade::route('/create'),
            'edit'   => EditGrade::route('/{record}/edit'),
        ];
    }
}
