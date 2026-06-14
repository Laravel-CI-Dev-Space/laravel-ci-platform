<?php

namespace App\Filament\Resources\Questions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuestionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Question')
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
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'published' => 'Publiée',
                            'hidden'    => 'Masquée',
                            'closed'    => 'Clôturée',
                            'deleted'   => 'Supprimée',
                            default     => $state,
                        })
                        ->color(fn (string $state): string => match ($state) {
                            'published' => 'success',
                            'hidden'    => 'gray',
                            'closed'    => 'info',
                            'deleted'   => 'danger',
                            default     => 'gray',
                        }),

                    TextEntry::make('tags.name')
                        ->label('Tags')
                        ->badge()
                        ->placeholder('Aucun tag'),

                    TextEntry::make('created_at')
                        ->label('Créée le')
                        ->dateTime('d/m/Y à H:i'),
                ]),

            Section::make('Contenu')
                ->schema([
                    TextEntry::make('body')
                        ->label('')
                        ->markdown()
                        ->prose(),
                ]),

            Section::make('Statistiques')
                ->columns(4)
                ->schema([
                    TextEntry::make('views_count')
                        ->label('Vues'),

                    TextEntry::make('votes_score')
                        ->label('Score votes'),

                    TextEntry::make('answers_count')
                        ->label('Réponses'),

                    TextEntry::make('comments_count')
                        ->label('Commentaires'),

                    TextEntry::make('last_activity_at')
                        ->label('Dernière activité')
                        ->dateTime('d/m/Y à H:i')
                        ->placeholder('—'),

                    TextEntry::make('acceptedAnswer.body')
                        ->label('Réponse acceptée')
                        ->placeholder('Aucune')
                        ->limit(200)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
