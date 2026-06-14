<?php

declare(strict_types=1);

namespace App\Filament\Resources\Articles\Schemas;

use App\Enums\ArticleLevel;
use App\Enums\ArticleStatus;
use App\Models\Article;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ArticleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Détails de l\'article')
                ->columns(2)
                ->schema([
                    TextEntry::make('title')
                        ->label('Titre')
                        ->columnSpanFull(),

                    TextEntry::make('author.name')
                        ->label('Auteur'),

                    TextEntry::make('reviewer.name')
                        ->label('Révisé par')
                        ->placeholder('—'),

                    TextEntry::make('level')
                        ->label('Niveau')
                        ->badge()
                        ->color(fn (ArticleLevel $state): string => $state->color())
                        ->formatStateUsing(fn (ArticleLevel $state): string => $state->label()),

                    TextEntry::make('status')
                        ->label('Statut')
                        ->badge()
                        ->color(fn (ArticleStatus $state): string => $state->color())
                        ->formatStateUsing(fn (ArticleStatus $state): string => $state->label()),

                    TextEntry::make('views_count')
                        ->label('Vues'),

                    TextEntry::make('comments_count')
                        ->label('Commentaires'),

                    TextEntry::make('published_at')
                        ->label('Publié le')
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('—'),

                    TextEntry::make('reviewed_at')
                        ->label('Révisé le')
                        ->dateTime('d/m/Y H:i')
                        ->placeholder('—'),

                    TextEntry::make('created_at')
                        ->label('Créé le')
                        ->dateTime('d/m/Y H:i'),
                ]),

            Section::make('Extrait')
                ->schema([
                    TextEntry::make('excerpt')
                        ->label('')
                        ->columnSpanFull()
                        ->placeholder('Aucun extrait.'),
                ]),

            Section::make('Corps')
                ->schema([
                    TextEntry::make('body')
                        ->label('')
                        ->columnSpanFull()
                        ->prose(),
                ]),

            Section::make('Raison du rejet')
                ->visible(fn (Article $record): bool => $record->status === ArticleStatus::Rejected)
                ->schema([
                    TextEntry::make('rejection_reason')
                        ->label('')
                        ->columnSpanFull()
                        ->placeholder('Aucune raison renseignée.'),
                ]),
        ]);
    }
}
