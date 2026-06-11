<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Enums\Events\EventStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
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
        ]);
    }
}
