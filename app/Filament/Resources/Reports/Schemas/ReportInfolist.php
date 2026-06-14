<?php

namespace App\Filament\Resources\Reports\Schemas;

use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\Questions\QuestionResource;
use App\Models\Answer;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Question;
use App\Models\Report;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Signalement')
                ->columns(2)
                ->schema([
                    TextEntry::make('reportable_type')
                        ->label('Type de contenu')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            Question::class => 'Question',
                            Article::class  => 'Article',
                            Comment::class  => 'Commentaire',
                            Answer::class   => 'Réponse',
                            default         => class_basename($state),
                        }),

                    TextEntry::make('reporter.name')
                        ->label('Signalé par'),

                    TextEntry::make('reason')
                        ->label('Motif')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'spam'           => 'Spam',
                            'inappropriate'  => 'Contenu inapproprié',
                            'harassment'     => 'Harcèlement',
                            'misinformation' => 'Désinformation',
                            'copyright'      => 'Droit d\'auteur',
                            'other'          => 'Autre',
                            default          => $state,
                        }),

                    TextEntry::make('created_at')
                        ->label('Signalé le')
                        ->dateTime('d/m/Y à H:i'),

                    TextEntry::make('description')
                        ->label('Description du signalant')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ]),

            Section::make('Contenu signalé')
                ->schema([
                    TextEntry::make('reportable.title')
                        ->label('Titre')
                        ->visible(fn (Report $record) => $record->reportable instanceof Question || $record->reportable instanceof Article)
                        ->placeholder('—'),

                    TextEntry::make('reportable.body')
                        ->label('Contenu')
                        ->placeholder('— (contenu supprimé)')
                        ->markdown()
                        ->prose(),

                    TextEntry::make('reportable_link')
                        ->label('Lien')
                        ->state(function (Report $record): ?string {
                            return match (true) {
                                $record->reportable instanceof Question => QuestionResource::getUrl('view', ['record' => $record->reportable]),
                                $record->reportable instanceof Article  => ArticleResource::getUrl('view', ['record' => $record->reportable]),
                                default                                 => null,
                            };
                        })
                        ->url(fn (?string $state) => $state)
                        ->placeholder('—')
                        ->openUrlInNewTab(),
                ]),

            Section::make('Traitement')
                ->columns(2)
                ->schema([
                    TextEntry::make('status')
                        ->label('Statut')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'pending'  => 'En attente',
                            'reviewed' => 'Examiné',
                            'resolved' => 'Résolu',
                            'rejected' => 'Rejeté',
                            default    => $state,
                        })
                        ->color(fn (string $state): string => match ($state) {
                            'pending'  => 'warning',
                            'reviewed' => 'info',
                            'resolved' => 'success',
                            'rejected' => 'gray',
                            default    => 'gray',
                        }),

                    TextEntry::make('handler.name')
                        ->label('Traité par')
                        ->placeholder('—'),

                    TextEntry::make('handled_at')
                        ->label('Traité le')
                        ->dateTime('d/m/Y à H:i')
                        ->placeholder('—'),

                    TextEntry::make('admin_note')
                        ->label('Note interne')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
