<?php

declare(strict_types=1);

namespace App\Filament\Resources\Questions\Schemas;

use App\Enums\QuestionStatus;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuestionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Détails de la question')
                ->columns(2)
                ->schema([
                    TextEntry::make('title')
                        ->label('Titre')
                        ->columnSpanFull(),

                    TextEntry::make('user.name')
                        ->label('Auteur'),

                    TextEntry::make('status')
                        ->label('Statut')
                        ->badge()
                        ->color(fn (QuestionStatus $state): string => $state->color())
                        ->formatStateUsing(fn (QuestionStatus $state): string => $state->label()),

                    IconEntry::make('is_pinned')
                        ->label('Épinglé')
                        ->boolean(),

                    TextEntry::make('votes_score')
                        ->label('Score de votes'),

                    TextEntry::make('answers_count')
                        ->label('Réponses'),

                    TextEntry::make('views_count')
                        ->label('Vues'),

                    TextEntry::make('last_activity_at')
                        ->label('Dernière activité')
                        ->dateTime('d/m/Y H:i'),

                    TextEntry::make('created_at')
                        ->label('Créé le')
                        ->dateTime('d/m/Y H:i'),
                ]),

            Section::make('Corps')
                ->schema([
                    TextEntry::make('body')
                        ->label('')
                        ->columnSpanFull()
                        ->prose(),
                ]),
        ]);
    }
}
