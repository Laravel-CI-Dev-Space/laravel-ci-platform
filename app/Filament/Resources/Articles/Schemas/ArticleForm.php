<?php

declare(strict_types=1);

namespace App\Filament\Resources\Articles\Schemas;

use App\Enums\ArticleLevel;
use App\Enums\ArticleStatus;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Article')
                ->columns(2)
                ->schema([
                    TextInput::make('author_label')
                        ->label('Auteur affiché')
                        ->placeholder('Laravel CI')
                        ->helperText('Laissez vide pour afficher le nom réel de l\'auteur')
                        ->maxLength(100)
                        ->columnSpanFull(),

                    TextInput::make('title')
                        ->label('Titre')
                        ->required()
                        ->minLength(10)
                        ->maxLength(300)
                        ->columnSpanFull(),

                    Textarea::make('excerpt')
                        ->label('Extrait')
                        ->maxLength(500)
                        ->rows(2)
                        ->columnSpanFull(),

                    Textarea::make('body')
                        ->label('Corps (Markdown)')
                        ->required()
                        ->rows(15)
                        ->columnSpanFull(),

                    FileUpload::make('cover_image')
                        ->label('Image de couverture')
                        ->image()
                        ->disk('assets')
                        ->directory('covers')
                        ->imagePreviewHeight('160')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(5120)
                        ->helperText('JPEG, PNG ou WebP · max 5 Mo')
                        ->columnSpanFull(),

                    Select::make('level')
                        ->label('Niveau')
                        ->options(ArticleLevel::options())
                        ->required(),

                    Select::make('status')
                        ->label('Statut')
                        ->options(ArticleStatus::options())
                        ->required(),

                    Textarea::make('rejection_reason')
                        ->label('Raison du rejet')
                        ->maxLength(1000)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
