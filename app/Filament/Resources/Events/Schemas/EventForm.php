<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\Schemas;

use App\Enums\EventStatus;
use App\Enums\EventType;
use App\Models\Event;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Informations générales')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Titre')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $operation, ?string $state, callable $set): void {
                            if ($operation === 'create' && $state) {
                                $set('slug', Str::slug($state));
                            }
                        })
                        ->columnSpanFull(),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(table: 'events', column: 'slug', ignoreRecord: true)
                        ->helperText('Généré automatiquement depuis le titre.')
                        ->columnSpanFull(),

                    Select::make('type')
                        ->label('Type')
                        ->options(EventType::options())
                        ->required()
                        ->default(EventType::Meetup->value),

                    Select::make('status')
                        ->label('Statut')
                        ->options(EventStatus::options())
                        ->required()
                        ->default(EventStatus::Draft->value),
                ]),

            Section::make('Date et lieu')
                ->columns(2)
                ->schema([
                    DateTimePicker::make('starts_at')
                        ->label('Début')
                        ->required()
                        ->native(false),

                    DateTimePicker::make('ends_at')
                        ->label('Fin')
                        ->required()
                        ->native(false)
                        ->after('starts_at'),

                    TextInput::make('location')
                        ->label('Lieu physique')
                        ->maxLength(255)
                        ->placeholder('Jokkolabs, Cocody, Abidjan')
                        ->helperText('Adresse physique de l\'événement.'),

                    TextInput::make('online_url')
                        ->label('Lien en ligne')
                        ->url()
                        ->maxLength(255)
                        ->placeholder('https://meet.google.com/...')
                        ->helperText('Lien Zoom, Google Meet, etc.'),
                ]),

            Section::make('Contenu')
                ->schema([
                    RichEditor::make('description')
                        ->label('Description')
                        ->required()
                        ->toolbarButtons([
                            'bold', 'italic', 'underline', 'strike',
                            'h2', 'h3', 'bulletList', 'orderedList',
                            'link', 'blockquote', 'codeBlock',
                        ])
                        ->columnSpanFull(),

                    RichEditor::make('program')
                        ->label('Programme / Agenda')
                        ->toolbarButtons([
                            'bold', 'italic', 'h2', 'h3',
                            'bulletList', 'orderedList', 'link',
                        ])
                        ->helperText('Optionnel — détail du déroulement.')
                        ->columnSpanFull(),

                    FileUpload::make('cover_image')
                        ->label('Image de couverture')
                        ->image()
                        ->disk('assets')
                        ->directory('web/img/events')
                        ->columnSpanFull()
                        ->helperText('Image affichée sur la page de l\'événement.'),
                ]),

            Section::make('Inscriptions')
                ->columns(2)
                ->schema([
                    TextInput::make('capacity')
                        ->label('Capacité maximale')
                        ->numeric()
                        ->minValue(1)
                        ->placeholder('Laisser vide = illimité')
                        ->helperText('Nombre maximal de participants. Vide = pas de limite.'),

                    TextInput::make('registrations_count')
                        ->label('Inscrits actuels')
                        ->numeric()
                        ->disabled()
                        ->helperText('Géré automatiquement par le système.'),

                    Toggle::make('waitlist_enabled')
                        ->label('Activer la liste d\'attente')
                        ->helperText('Si l\'événement est complet, les nouveaux inscrits rejoignent la liste d\'attente.')
                        ->columnSpanFull(),
                ]),

            Section::make('Inscriptions externes')
                ->columns(2)
                ->schema([
                    Toggle::make('guest_registration_enabled')
                        ->label('Autoriser les inscriptions sans compte')
                        ->helperText('Les visiteurs non connectés pourront s\'inscrire avec leur nom, email et WhatsApp.')
                        ->columnSpanFull(),

                    Toggle::make('ticketing_enabled')
                        ->label('Activer la ticketerie')
                        ->helperText('Un numéro de ticket et un QR code seront générés à chaque inscription confirmée.')
                        ->live()
                        ->columnSpanFull(),

                    TextInput::make('ticket_prefix')
                        ->label('Préfixe de ticket')
                        ->maxLength(10)
                        ->placeholder('LCI')
                        ->helperText('Ex. LCI → LCI-5-K3M9P2. Laisser vide = LCI par défaut.')
                        ->visible(fn (callable $get): bool => (bool) $get('ticketing_enabled')),
                ]),

            Section::make('Tarification')
                ->columns(2)
                ->schema([
                    Toggle::make('is_paid')
                        ->label('Événement payant')
                        ->helperText('Activez si une participation financière est requise.')
                        ->live()
                        ->columnSpanFull(),

                    TextInput::make('price')
                        ->label('Prix')
                        ->numeric()
                        ->minValue(0)
                        ->prefix('XOF')
                        ->placeholder('5000')
                        ->visible(fn (callable $get): bool => (bool) $get('is_paid'))
                        ->required(fn (callable $get): bool => (bool) $get('is_paid')),

                    Select::make('currency')
                        ->label('Devise')
                        ->options([
                            'XOF' => 'Franc CFA (XOF)',
                            'EUR' => 'Euro (EUR)',
                            'USD' => 'Dollar US (USD)',
                            'GBP' => 'Livre sterling (GBP)',
                        ])
                        ->default('XOF')
                        ->visible(fn (callable $get): bool => (bool) $get('is_paid')),

                    Section::make('Code promotionnel')
                        ->columnSpanFull()
                        ->visible(fn (callable $get): bool => (bool) $get('is_paid'))
                        ->description('Le code promo n\'est jamais affiché publiquement — il doit être communiqué manuellement aux participants.')
                        ->columns(2)
                        ->schema([
                            TextInput::make('promo_code')
                                ->label('Code promo')
                                ->maxLength(50)
                                ->placeholder('LAUNCH50')
                                ->helperText('Insensible à la casse. Partagez ce code par email ou WhatsApp.')
                                ->columnSpanFull()
                                ->default(fn (): string => strtoupper(Str::random(8)))
                                ->hintAction(
                                    Action::make('generate_promo_code')
                                        ->label('Générer un code')
                                        ->icon('heroicon-o-arrow-path')
                                        ->action(fn (Set $set) => $set('promo_code', strtoupper(Str::random(8))))
                                ),

                            Select::make('promo_discount_type')
                                ->label('Type de réduction')
                                ->options([
                                    'percent' => 'Pourcentage (%)',
                                    'fixed'   => 'Montant fixe',
                                ])
                                ->live(),

                            TextInput::make('promo_discount_value')
                                ->label('Valeur de la réduction')
                                ->numeric()
                                ->minValue(0)
                                ->placeholder(fn (callable $get): string => $get('promo_discount_type') === 'percent' ? '20' : '1000')
                                ->suffix(fn (callable $get): string => $get('promo_discount_type') === 'percent' ? '%' : 'XOF'),

                            DateTimePicker::make('promo_expires_at')
                                ->label('Expiration du code')
                                ->native(false)
                                ->minDate(now())
                                ->helperText('Laisser vide = pas d\'expiration.'),

                            TextInput::make('promo_max_uses')
                                ->label('Nombre max d\'utilisations')
                                ->numeric()
                                ->minValue(1)
                                ->placeholder('Laisser vide = illimité'),

                            TextInput::make('promo_uses_count')
                                ->label('Utilisations actuelles')
                                ->numeric()
                                ->disabled()
                                ->helperText('Géré automatiquement.'),
                        ]),
                ]),

            Section::make('Récapitulatif post-événement')
                ->visible(fn (callable $get): bool => $get('status') === 'completed')
                ->schema([
                    Placeholder::make('recap_status')
                        ->label('Statut du récapitulatif')
                        ->content(fn (?Event $record): string => $record?->hasRecap()
                            ? 'Publié le ' . $record->recap_published_at->translatedFormat('d/m/Y à H:i')
                            : 'Non publié'),

                    Textarea::make('recap_summary')
                        ->label('Résumé')
                        ->rows(3)
                        ->maxLength(1000)
                        ->columnSpanFull(),

                    RichEditor::make('recap_content')
                        ->label('Contenu détaillé')
                        ->toolbarButtons([
                            'bold', 'italic', 'underline', 'strike',
                            'h2', 'h3', 'bulletList', 'orderedList',
                            'link', 'blockquote', 'codeBlock',
                        ])
                        ->columnSpanFull(),

                    Repeater::make('photos')
                        ->label('Photos')
                        ->relationship()
                        ->schema([
                            FileUpload::make('path')
                                ->label('Image')
                                ->image()
                                ->disk('assets')
                                ->directory('events/recap')
                                ->required(),

                            TextInput::make('caption')
                                ->label('Légende')
                                ->maxLength(255),
                        ])
                        ->columns(2)
                        ->maxItems(10)
                        ->reorderable()
                        ->orderColumn('order')
                        ->columnSpanFull(),

                    TextInput::make('recap_video_url_1')
                        ->label('Vidéo 1 (URL)')
                        ->url()
                        ->maxLength(255),

                    TextInput::make('recap_video_url_2')
                        ->label('Vidéo 2 (URL)')
                        ->url()
                        ->maxLength(255),

                    TextInput::make('recap_video_url_3')
                        ->label('Vidéo 3 (URL)')
                        ->url()
                        ->maxLength(255),

                    FileUpload::make('recap_document_path')
                        ->label('Document de récapitulatif')
                        ->disk('assets')
                        ->directory('documents/events')
                        ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                        ->maxSize(10240)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
