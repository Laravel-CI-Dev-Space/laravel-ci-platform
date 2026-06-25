<?php

namespace App\Filament\Resources\MemberCards;

use App\Filament\Resources\MemberCards\Pages\EditMemberCard;
use App\Filament\Resources\MemberCards\Pages\ListMemberCards;
use App\Filament\Resources\MemberCards\Schemas\MemberCardForm;
use App\Filament\Resources\MemberCards\Tables\MemberCardsTable;
use App\Models\MemberCard;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MemberCardResource extends Resource
{
    protected static ?string $model = MemberCard::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $navigationLabel = 'Cartes membres';

    protected static ?string $navigationGroup = 'Communauté';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Carte membre';

    protected static ?string $pluralModelLabel = 'Cartes membres';

    public static function form(Schema $schema): Schema
    {
        return MemberCardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MemberCardsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMemberCards::route('/'),
            'edit'  => EditMemberCard::route('/{record}/edit'),
        ];
    }
}
