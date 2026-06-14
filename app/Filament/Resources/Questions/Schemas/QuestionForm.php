<?php

declare(strict_types=1);

namespace App\Filament\Resources\Questions\Schemas;

use App\Enums\QuestionStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Question')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Titre')
                        ->required()
                        ->minLength(10)
                        ->maxLength(300)
                        ->columnSpanFull(),

                    Textarea::make('body')
                        ->label('Corps (Markdown)')
                        ->required()
                        ->rows(10)
                        ->columnSpanFull(),

                    Select::make('status')
                        ->label('Statut')
                        ->options(QuestionStatus::options())
                        ->required(),

                    Toggle::make('is_pinned')
                        ->label('Épinglé')
                        ->onColor('success')
                        ->offColor('danger'),
                ]),
        ]);
    }
}
