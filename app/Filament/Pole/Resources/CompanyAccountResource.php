<?php

namespace App\Filament\Pole\Resources;

use App\Enums\CompanyAccountStatus;
use App\Enums\UserRole;
use App\Models\Company;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CompanyAccountResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationLabel = 'Entreprises partenaires';

    protected static ?string $modelLabel = 'Entreprise';

    protected static ?string $pluralModelLabel = 'Entreprises';

    

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(UserRole::PolePartenariat->value) ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Entreprise')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->copyable()
                    ->placeholder('—'),

                TextColumn::make('city')
                    ->label('Ville')
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (CompanyAccountStatus $state) => match ($state) {
                        CompanyAccountStatus::Active    => 'success',
                        CompanyAccountStatus::Pending   => 'warning',
                        CompanyAccountStatus::Suspended => 'danger',
                        CompanyAccountStatus::Rejected  => 'gray',
                    })
                    ->formatStateUsing(fn (CompanyAccountStatus $state) => match ($state) {
                        CompanyAccountStatus::Active    => 'Active',
                        CompanyAccountStatus::Pending   => 'En attente',
                        CompanyAccountStatus::Suspended => 'Suspendue',
                        CompanyAccountStatus::Rejected  => 'Rejetée',
                    }),

                IconColumn::make('is_verified')
                    ->label('Vérifiée')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Inscrite le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        CompanyAccountStatus::Active->value    => 'Active',
                        CompanyAccountStatus::Pending->value   => 'En attente',
                        CompanyAccountStatus::Suspended->value => 'Suspendue',
                    ]),
            ])
            ->headerActions([
                \Filament\Tables\Actions\ExportAction::make()
                    ->label('Exporter (Excel)')
                    ->color('gray'),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Pole\Resources\CompanyAccountResource\Pages\ListCompanyAccounts::route('/'),
        ];
    }
}
