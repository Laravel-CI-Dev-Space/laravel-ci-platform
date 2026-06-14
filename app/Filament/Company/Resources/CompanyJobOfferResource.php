<?php

declare(strict_types=1);

namespace App\Filament\Company\Resources;

use App\Enums\JobContractType;
use App\Enums\JobLevel;
use App\Enums\JobOfferStatus;
use App\Filament\Company\Resources\CompanyJobOfferResource\Pages\CreateCompanyJobOffer;
use App\Filament\Company\Resources\CompanyJobOfferResource\Pages\ListCompanyJobOffers;
use App\Filament\Company\Resources\CompanyJobOfferResource\Pages\ViewCompanyJobOffer;
use App\Models\JobOffer;
use App\Models\JobOfferCategory;
use App\Models\JobSkill;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
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
            Section::make('Informations principales')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Intitulé du poste')->required()->maxLength(200)->columnSpanFull(),

                    Select::make('contract_type')
                        ->label('Type de contrat')
                        ->options(JobContractType::options())
                        ->required(),

                    Select::make('level')
                        ->label('Niveau')
                        ->options(JobLevel::options())
                        ->required(),

                    TextInput::make('location')->label('Ville')->nullable(),
                    TextInput::make('country')->label('Pays')->default("Côte d'Ivoire"),
                    Toggle::make('is_remote')->label('Télétravail'),
                    Toggle::make('is_urgent')->label('Urgente'),
                    TextInput::make('salary_min')->label('Salaire min (FCFA)')->numeric()->minValue(0)->nullable(),
                    TextInput::make('salary_max')->label('Salaire max (FCFA)')->numeric()->minValue(0)->gte('salary_min')->nullable(),
                    Toggle::make('salary_visible')->label('Afficher le salaire')->default(true),

                    Textarea::make('description')
                        ->label('Description du poste')->required()->minLength(100)->rows(10)->columnSpanFull(),
                ]),

            Section::make('Catégories & Compétences')
                ->columns(2)
                ->schema([
                    Select::make('categories')
                        ->label('Catégories')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->options(JobOfferCategory::orderBy('name')->pluck('name', 'id'))
                        ->required(),

                    Select::make('skills')
                        ->label('Compétences requises (max 10)')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->options(JobSkill::orderBy('name')->pluck('name', 'id'))
                        ->required(),
                ]),

            Section::make('Médias')
                ->description('Image de couverture et fiche de poste (PDF ou Word)')
                ->columns(2)
                ->schema([
                    FileUpload::make('cover_image')
                        ->label('Image de couverture')
                        ->image()
                        ->maxSize(2048)
                        ->disk('assets')          // → public/assets/job-covers/
                        ->directory('job-covers')
                        ->visibility('public')
                        ->nullable()
                        ->helperText('JPG, PNG, WebP — max 2 Mo'),

                    FileUpload::make('attachment_path')
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
                    ->formatStateUsing(fn (JobContractType $state): string => $state->label()),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (JobOfferStatus $state): string => $state->color())
                    ->formatStateUsing(fn (JobOfferStatus $state): string => match ($state) {
                        JobOfferStatus::Pending => 'En validation',
                        default                 => $state->label(),
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
                    ->options(JobOfferStatus::options()),
            ])

            ->actions([
                ViewAction::make(),
                Action::make('mark_filled')
                    ->label('Marquer comme pourvue')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (JobOffer $r): bool => $r->status === JobOfferStatus::Active)
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
