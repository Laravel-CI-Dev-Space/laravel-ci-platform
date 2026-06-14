<?php

declare(strict_types=1);

namespace App\Filament\Resources\JobOffers;

use App\Filament\Resources\Concerns\AuthorizesViaPermission;
use App\Filament\Resources\JobOffers\Pages\EditJobOffer;
use App\Filament\Resources\JobOffers\Pages\ListJobOffers;
use App\Filament\Resources\JobOffers\Pages\ViewJobOffer;
use App\Filament\Resources\JobOffers\Schemas\JobOfferForm;
use App\Filament\Resources\JobOffers\Schemas\JobOfferInfolist;
use App\Filament\Resources\JobOffers\Tables\JobOffersTable;
use App\Models\JobOffer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JobOfferResource extends Resource
{
    use AuthorizesViaPermission;

    protected static ?string $model = JobOffer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'Offres';

    protected static ?string $modelLabel = "Offre d'emploi";

    protected static ?string $pluralModelLabel = "Offres d'emploi";

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Job Board';
    }

    public static function form(Schema $schema): Schema
    {
        return JobOfferForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return JobOfferInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JobOffersTable::configure($table);
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
            'index' => ListJobOffers::route('/'),
            'view'  => ViewJobOffer::route('/{record}'),
            'edit'  => EditJobOffer::route('/{record}/edit'),
        ];
    }
}
