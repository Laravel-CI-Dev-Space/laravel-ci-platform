<?php

namespace App\Filament\Resources\Articles\Schemas;

use App\Models\Article;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ArticleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Article')
                ->columns(2)
                ->schema([
                    TextEntry::make('title')
                        ->label('Titre')
                        ->columnSpanFull(),

                    TextEntry::make('author.name')
                        ->label('Auteur'),

                    TextEntry::make('level')
                        ->label('Niveau')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => Article::$levelLabels[$state] ?? $state)
                        ->color(fn (string $state): string => Article::$levelColors[$state] ?? 'gray'),

                    TextEntry::make('status')
                        ->label('Statut')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'draft'     => 'Brouillon',
                            'pending'   => 'En attente',
                            'published' => 'Publié',
                            'rejected'  => 'Rejeté',
                            default     => $state,
                        })
                        ->color(fn (string $state): string => match ($state) {
                            'draft'     => 'gray',
                            'pending'   => 'warning',
                            'published' => 'success',
                            'rejected'  => 'danger',
                            default     => 'gray',
                        }),

                    TextEntry::make('tags.name')
                        ->label('Tags')
                        ->badge()
                        ->placeholder('Aucun tag'),

                    TextEntry::make('created_at')
                        ->label('Créé le')
                        ->dateTime('d/m/Y à H:i'),
                ]),

            Section::make('Contenu')
                ->schema([
                    TextEntry::make('excerpt')
                        ->label('Extrait')
                        ->placeholder('—'),

                    TextEntry::make('body')
                        ->label('')
                        ->markdown()
                        ->prose(),
                ]),

            Section::make('Modération')
                ->columns(2)
                ->schema([
                    TextEntry::make('reviewer.name')
                        ->label('Validé par')
                        ->placeholder('—'),

                    TextEntry::make('reviewed_at')
                        ->label('Le')
                        ->dateTime('d/m/Y à H:i')
                        ->placeholder('—'),

                    TextEntry::make('rejection_reason')
                        ->label('Motif du rejet')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ]),

            Section::make('Statistiques')
                ->columns(3)
                ->schema([
                    TextEntry::make('views_count')
                        ->label('Vues'),

                    TextEntry::make('comments_count')
                        ->label('Commentaires'),

                    TextEntry::make('published_at')
                        ->label('Publié le')
                        ->dateTime('d/m/Y à H:i')
                        ->placeholder('—'),
                ]),
        ]);
    }
}
