<?php

namespace App\Filament\Pole\Resources;

use App\Enums\JobOfferStatus;
use App\Enums\UserRole;
use App\Models\JobOffer;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JobOfferResource extends Resource
{
    protected static ?string $model = JobOffer::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'Offres d\'emploi';

    protected static ?string $modelLabel = 'Offre d\'emploi';

    protected static ?string $pluralModelLabel = 'Offres d\'emploi';

    

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(UserRole::PoleEmployabilite->value) ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Intitulé du poste')
                ->required()
                ->maxLength(255),

            TextInput::make('company_name')
                ->label('Entreprise')
                ->required()
                ->maxLength(150),

            Textarea::make('description')
                ->label('Description')
                ->rows(5)
                ->required(),

            Select::make('status')
                ->label('Statut')
                ->options([
                    JobOfferStatus::Draft->value  => 'Brouillon',
                    JobOfferStatus::Active->value => 'Actif',
                ])
                ->required()
                ->default(JobOfferStatus::Draft->value),

            Toggle::make('is_urgent')
                ->label('Urgent'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Poste')
                    ->searchable()
                    ->limit(45),

                TextColumn::make('company_name')
                    ->label('Entreprise')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (JobOfferStatus $state) => match ($state) {
                        JobOfferStatus::Active   => 'success',
                        JobOfferStatus::Expired  => 'warning',
                        JobOfferStatus::Rejected => 'danger',
                        default                  => 'gray',
                    })
                    ->formatStateUsing(fn (JobOfferStatus $state) => match ($state) {
                        JobOfferStatus::Draft    => 'Brouillon',
                        JobOfferStatus::Pending  => 'En attente',
                        JobOfferStatus::Active   => 'Active',
                        JobOfferStatus::Expired  => 'Expirée',
                        JobOfferStatus::Rejected => 'Rejetée',
                        JobOfferStatus::Filled   => 'Pourvue',
                    }),

                IconColumn::make('is_urgent')
                    ->label('Urgent')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        JobOfferStatus::Draft->value  => 'Brouillon',
                        JobOfferStatus::Active->value => 'Active',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('activate')
                    ->label('Publier')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (JobOffer $record) => $record->status !== JobOfferStatus::Active)
                    ->action(fn (JobOffer $record) => $record->update(['status' => JobOfferStatus::Active]))
                    ->requiresConfirmation(),
                Action::make('archive')
                    ->label('Archiver')
                    ->icon(Heroicon::OutlinedArchiveBox)
                    ->color('gray')
                    ->visible(fn (JobOffer $record) => $record->status === JobOfferStatus::Active)
                    ->action(fn (JobOffer $record) => $record->update(['status' => JobOfferStatus::Expired]))
                    ->requiresConfirmation(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Filament\Pole\Resources\JobOfferResource\Pages\ListJobOffers::route('/'),
            'create' => \App\Filament\Pole\Resources\JobOfferResource\Pages\CreateJobOffer::route('/create'),
            'edit'   => \App\Filament\Pole\Resources\JobOfferResource\Pages\EditJobOffer::route('/{record}/edit'),
        ];
    }
}
