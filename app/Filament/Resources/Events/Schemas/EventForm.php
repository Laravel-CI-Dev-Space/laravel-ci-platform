<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Enums\Events\EventMediaType;
use App\Enums\Events\EventStatus;
use App\Support\EventMediaFormData;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Événement')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    TextInput::make('title')
                        ->label('Titre')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->label('Description')
                        ->required()
                        ->rows(6)
                        ->columnSpanFull(),

                    Select::make('type_id')
                        ->label('Type')
                        ->relationship('type', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('status')
                        ->label('Statut')
                        ->options(EventStatus::options())
                        ->default(EventStatus::DRAFT->value)
                        ->required(),

                    DateTimePicker::make('start_date')
                        ->label('Début')
                        ->required()
                        ->native(false),

                    DateTimePicker::make('end_date')
                        ->label('Fin')
                        ->required()
                        ->native(false)
                        ->after('start_date'),

                    TextInput::make('location')
                        ->label('Lieu')
                        ->maxLength(255),

                    TextInput::make('meeting_link')
                        ->label('Lien visio')
                        ->url()
                        ->maxLength(255),

                    TextInput::make('capacity')
                        ->label('Capacité')
                        ->numeric()
                        ->minValue(1),

                    FileUpload::make('cover')
                        ->label('Cover')
                        ->disk('public')
                        ->directory('events')
                        ->image()
                        ->fetchFileInformation(false)
                        ->columnSpanFull(),
                ]),

            Section::make('Médias & replays')
                ->description('Photos, enregistrements ou PDF visibles sur la fiche une fois l\'événement passé.')
                ->columnSpanFull()
                ->schema([
                    Repeater::make('media')
                        ->relationship()
                        ->label('Médias')
                        ->schema([
                            Select::make('type')
                                ->label('Type')
                                ->options(EventMediaType::options())
                                ->required()
                                ->live(),

                            TextInput::make('video_url')
                                ->label('URL de la vidéo')
                                ->url()
                                ->maxLength(500)
                                ->required(fn (Get $get) => $get('type') === EventMediaType::VIDEO->value)
                                ->visible(fn (Get $get) => $get('type') === EventMediaType::VIDEO->value),

                            FileUpload::make('image_path')
                                ->label('Image')
                                ->disk('public')
                                ->directory('events/media')
                                ->image()
                                ->fetchFileInformation(false)
                                ->required(fn (Get $get) => $get('type') === EventMediaType::IMAGE->value)
                                ->visible(fn (Get $get) => $get('type') === EventMediaType::IMAGE->value),

                            FileUpload::make('document_path')
                                ->label('PDF')
                                ->disk('public')
                                ->directory('events/media')
                                ->acceptedFileTypes(['application/pdf'])
                                ->fetchFileInformation(false)
                                ->required(fn (Get $get) => $get('type') === EventMediaType::PDF->value)
                                ->visible(fn (Get $get) => $get('type') === EventMediaType::PDF->value),
                        ])
                        ->mutateRelationshipDataBeforeFillUsing(
                            fn (array $data): array => EventMediaFormData::hydrate($data),
                        )
                        ->mutateRelationshipDataBeforeCreateUsing(
                            fn (array $data): array => EventMediaFormData::prepareForSave($data),
                        )
                        ->mutateRelationshipDataBeforeSaveUsing(
                            fn (array $data): array => EventMediaFormData::prepareForSave($data),
                        )
                        ->columns(2)
                        ->defaultItems(0)
                        ->addActionLabel('Ajouter un média')
                        ->collapsible(),
                ]),
        ]);
    }
}
