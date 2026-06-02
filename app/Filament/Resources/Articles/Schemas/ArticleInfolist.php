<?php

declare(strict_types=1);

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
                        ->color(fn (string $state): string => match ($state) {
                            'beginner'     => 'success',
                            'intermediate' => 'warning',
                            'advanced'     => 'danger',
                            default        => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'beginner'     => 'Débutant',
                            'intermediate' => 'Intermédiaire',
                            'advanced'     => 'Avancé',
                            default        => $state,
                        }),

                    TextEntry::make('status')
                        ->label('Statut')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'draft'     => 'gray',
                            'pending'   => 'warning',
                            'published' => 'success',
                            'rejected'  => 'danger',
                            default     => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'draft'     => 'Brouillon',
                            'pending'   => 'En attente',
                            'published' => 'Publié',
                            'rejected'  => 'Rejeté',
                            default     => $state,
                        }),

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
                ->visible(fn (Article $record): bool => $record->status === 'rejected')
                ->schema([
                    TextEntry::make('rejection_reason')
                        ->label('')
                        ->columnSpanFull()
                        ->placeholder('Aucune raison renseignée.'),
                ]),
        ]);
    }
}
