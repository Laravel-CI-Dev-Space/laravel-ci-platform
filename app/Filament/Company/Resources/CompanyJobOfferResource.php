<?php

declare(strict_types=1);

namespace App\Filament\Company\Resources;

use App\Filament\Company\Resources\CompanyJobOfferResource\Pages\CreateCompanyJobOffer;
use App\Filament\Company\Resources\CompanyJobOfferResource\Pages\ListCompanyJobOffers;
use App\Filament\Company\Resources\CompanyJobOfferResource\Pages\ViewCompanyJobOffer;
use App\Models\JobOffer;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CompanyJobOfferResource extends Resource
{
    protected static ?string $model = JobOffer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'Mes offres';

    protected static ?string $modelLabel = "Offre d'emploi";

    protected static ?string $pluralModelLabel = "Mes offres d'emploi";

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Recrutement';
    }

    /** Restreint les offres à la company authentifiée. */
    public static function getEloquentQuery(): Builder
    {
        $account = auth('company')->user();

        return parent::getEloquentQuery()
            ->where('company_id', $account?->company_id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('Informations principales')
                ->columns(2)
                ->schema([
                    \Filament\Forms\Components\TextInput::make('title')
                        ->label('Intitulé du poste')->required()->maxLength(200)->columnSpanFull(),

                    \Filament\Forms\Components\Select::make('contract_type')
                        ->label('Type de contrat')
                        ->options(['cdi' => 'CDI', 'cdd' => 'CDD', 'freelance' => 'Freelance', 'internship' => 'Stage', 'apprenticeship' => 'Alternance'])
                        ->required(),

                    \Filament\Forms\Components\Select::make('level')
                        ->label('Niveau')
                        ->options(['junior' => 'Junior', 'intermediate' => 'Intermédiaire', 'senior' => 'Senior', 'lead' => 'Lead', 'any' => 'Tous niveaux'])
                        ->required(),

                    \Filament\Forms\Components\TextInput::make('location')->label('Ville')->nullable(),
                    \Filament\Forms\Components\TextInput::make('country')->label('Pays')->default("Côte d'Ivoire"),
                    \Filament\Forms\Components\Toggle::make('is_remote')->label('Télétravail'),
                    \Filament\Forms\Components\Toggle::make('is_urgent')->label('Urgente'),
                    \Filament\Forms\Components\TextInput::make('salary_min')->label('Salaire min (FCFA)')->numeric()->nullable(),
                    \Filament\Forms\Components\TextInput::make('salary_max')->label('Salaire max (FCFA)')->numeric()->nullable(),
                    \Filament\Forms\Components\Toggle::make('salary_visible')->label('Afficher le salaire')->default(true),

                    \Filament\Forms\Components\Textarea::make('description')
                        ->label('Description du poste')->required()->minLength(100)->rows(10)->columnSpanFull(),
                ]),

            \Filament\Schemas\Components\Section::make('Catégories & Compétences')
                ->columns(2)
                ->schema([
                    \Filament\Forms\Components\Select::make('categories')
                        ->label('Catégories')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->options(\App\Models\JobOfferCategory::orderBy('name')->pluck('name', 'id'))
                        ->required(),

                    \Filament\Forms\Components\Select::make('skills')
                        ->label('Compétences requises (max 10)')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->options(\App\Models\JobSkill::orderBy('name')->pluck('name', 'id'))
                        ->required(),
                ]),

            \Filament\Schemas\Components\Section::make('Médias')
                ->description('Image de couverture et fiche de poste (PDF ou Word)')
                ->columns(2)
                ->schema([
                    \Filament\Forms\Components\FileUpload::make('cover_image')
                        ->label('Image de couverture')
                        ->image()
                        ->maxSize(2048)
                        ->disk('assets')          // → public/assets/job-covers/
                        ->directory('job-covers')
                        ->visibility('public')
                        ->nullable()
                        ->helperText('JPG, PNG, WebP — max 2 Mo'),

                    \Filament\Forms\Components\FileUpload::make('attachment_path')
                        ->label('Fiche de poste (document)')
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        ])
                        ->maxSize(10240)
                        ->disk('assets')          // → public/assets/job-attachments/
                        ->directory('job-attachments')
                        ->visibility('public')
                        ->nullable()
                        ->storeFileNamesIn('attachment_name')
                        ->helperText('PDF, DOC, DOCX — max 10 Mo'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Intitulé')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('contract_type')
                    ->label('Contrat')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'cdi' => 'CDI', 'cdd' => 'CDD', 'freelance' => 'Freelance',
                        'internship' => 'Stage', 'apprenticeship' => 'Alternance', default => $state,
                    }),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'   => 'success',
                        'pending'  => 'warning',
                        'expired'  => 'danger',
                        'filled'   => 'info',
                        'rejected' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active'   => 'Active',
                        'pending'  => 'En validation',
                        'expired'  => 'Expirée',
                        'filled'   => 'Pourvue',
                        'rejected' => 'Refusée',
                        default    => ucfirst($state),
                    }),

                IconColumn::make('is_remote')->label('Remote')->boolean(),

                TextColumn::make('applications_count')
                    ->label('Candidatures')
                    ->sortable(),

                TextColumn::make('views_count')
                    ->label('Vues')
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Expire le')
                    ->dateTime('d/m/Y')
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'active'   => 'Active',
                        'pending'  => 'En validation',
                        'expired'  => 'Expirée',
                        'filled'   => 'Pourvue',
                        'rejected' => 'Refusée',
                    ]),
            ])

            ->actions([
                ViewAction::make(),
                Action::make('mark_filled')
                    ->label('Marquer comme pourvue')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (JobOffer $r): bool => $r->status === 'active')
                    ->requiresConfirmation()
                    ->action(function (JobOffer $record): void {
                        $record->update(['status' => 'filled']);
                        Notification::make()->title('Offre marquée comme pourvue')->success()->send();
                    }),
            ])

            ->headerActions([
                Action::make('create')
                    ->label('Publier une offre')
                    ->icon('heroicon-o-plus')
                    ->url(static::getUrl('create')),
            ])

            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCompanyJobOffers::route('/'),
            'create' => CreateCompanyJobOffer::route('/create'),
            'view'   => ViewCompanyJobOffer::route('/{record}'),
        ];
    }
}
