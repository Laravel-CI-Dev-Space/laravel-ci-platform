<?php

declare(strict_types=1);

namespace App\Filament\Resources\JobOffers\Schemas;

use App\Enums\JobContractType;
use App\Enums\JobLevel;
use App\Enums\JobOfferStatus;
use App\Models\Company;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobOfferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // ─── Entreprise ────────────────────────────────────────────────
            Section::make('Entreprise')
                ->description('Sélectionnez une entreprise de la plateforme ou saisissez un nom libre.')
                ->columns(2)
                ->schema([
                    Select::make('company_id')
                        ->label('Entreprise (plateforme)')
                        ->relationship('company', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->live()
                        ->placeholder('Aucune - saisir le nom ci-dessous'),

                    TextInput::make('company_name')
                        ->label('Nom de l\'entreprise externe')
                        ->maxLength(200)
                        ->placeholder('Ex : INFLUO, Atos, StartupXYZ')
                        ->helperText('Remplir uniquement si l\'entreprise n\'est pas sur la plateforme.')
                        ->visible(fn (Get $get) => blank($get('company_id'))),
                ]),

            // ─── Infos principales ─────────────────────────────────────────
            Section::make("Offre d'emploi")
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Intitulé du poste')
                        ->required()
                        ->maxLength(200)
                        ->columnSpanFull(),

                    Select::make('contract_type')
                        ->label('Type de contrat')
                        ->options(JobContractType::options())
                        ->required(),

                    Select::make('level')
                        ->label('Niveau séniorité')
                        ->options(JobLevel::options())
                        ->required(),

                    TextInput::make('location')
                        ->label('Lieu')
                        ->maxLength(150)
                        ->placeholder('Ex : Abidjan/Cocody'),

                    TextInput::make('experience_years')
                        ->label('Expérience min. (années)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(30)
                        ->suffix('ans'),

                    TagsInput::make('education_levels')
                        ->label('Niveau(x) académique(s)')
                        ->placeholder('Ex : BAC+3, BAC+4, BAC+5')
                        ->suggestions(['BAC+2', 'BAC+3', 'BAC+4', 'BAC+5', 'Doctorat']),

                    TagsInput::make('domains')
                        ->label('Domaine(s) / Métier(s)')
                        ->placeholder('Ex : Informatique, Génie logiciel')
                        ->suggestions(['Informatique', 'Informatique de Gestion', 'Génie logiciel', 'Réseaux & Télécoms', 'Cybersécurité', 'Data & IA']),

                    Toggle::make('is_remote')->label('Télétravail possible'),
                    Toggle::make('is_hybrid')->label('Hybride'),
                    Toggle::make('is_urgent')->label('Urgente'),

                    Select::make('status')
                        ->label('Statut')
                        ->options(JobOfferStatus::options())
                        ->default('active')
                        ->required(),

                    DatePicker::make('expires_at')
                        ->label('Date limite de candidature')
                        ->minDate(now()),

                    DatePicker::make('published_at')
                        ->label('Date de publication')
                        ->default(now()),
                ]),

            // ─── Contenu de l'offre ────────────────────────────────────────
            Section::make('Contenu de l\'offre')
                ->schema([
                    RichEditor::make('description')
                        ->label('Description du poste - missions')
                        ->required()
                        ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'h3', 'undo', 'redo']),

                    RichEditor::make('profile_description')
                        ->label('Profil du poste - compétences requises')
                        ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'h3', 'undo', 'redo']),

                    RichEditor::make('tech_stack')
                        ->label('Environnement technique & stack')
                        ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'h3', 'undo', 'redo']),

                    RichEditor::make('benefits')
                        ->label('Ce que l\'entreprise offre')
                        ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'h3', 'undo', 'redo']),
                ]),

            // ─── Candidature ───────────────────────────────────────────────
            Section::make('Comment postuler')
                ->description('Au moins un des deux champs est recommandé pour les offres externes.')
                ->columns(2)
                ->schema([
                    TextInput::make('apply_email')
                        ->label('Email de candidature')
                        ->email()
                        ->maxLength(200)
                        ->placeholder('recrutement@entreprise.ci'),

                    TextInput::make('apply_url')
                        ->label('Lien de candidature externe')
                        ->url()
                        ->maxLength(500)
                        ->placeholder('https://entreprise.ci/postuler'),
                ]),

            // ─── Admin ─────────────────────────────────────────────────────
            Section::make('Administration')
                ->columns(1)
                ->collapsed()
                ->schema([
                    Textarea::make('rejection_reason')
                        ->label('Raison du refus (si rejetée)')
                        ->rows(3),
                ]),
        ]);
    }
}
