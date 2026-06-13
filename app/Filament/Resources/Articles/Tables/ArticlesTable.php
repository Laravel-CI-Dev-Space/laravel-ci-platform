<?php

namespace App\Filament\Resources\Articles\Tables;

use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Support\FrenchActions;
use App\Models\Article;
use Filament\Actions\Action;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('author.name')
                    ->label('Auteur')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('level')
                    ->label('Niveau')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Article::$levelLabels[$state] ?? $state)
                    ->color(fn (string $state): string => Article::$levelColors[$state] ?? 'gray'),

                TextColumn::make('status')
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

                TextColumn::make('views_count')
                    ->label('Vues')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('comments_count')
                    ->label('Commentaires')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('published_at')
                    ->label('Publié le')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'draft'     => 'Brouillon',
                        'pending'   => 'En attente',
                        'published' => 'Publié',
                        'rejected'  => 'Rejeté',
                    ]),

                SelectFilter::make('level')
                    ->label('Niveau')
                    ->options(Article::$levelLabels),

                TrashedFilter::make(),
            ])
            ->actions([
                FrenchActions::viewPage(ArticleResource::class),

                FrenchActions::confirm(
                    Action::make('approve')
                        ->label('Publier')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Article $record) => $record->status === 'pending'),
                    'Publier cet article ?',
                    'L\'article sera visible publiquement sur le blog.',
                )->action(function (Article $record) {
                    $record->update([
                        'status'       => 'published',
                        'published_at' => now(),
                        'reviewed_by'  => auth()->id(),
                        'reviewed_at'  => now(),
                    ]);
                    Notification::make()->title('Article publié')->success()->send();
                }),

                Action::make('reject')
                    ->label('Rejeter')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Article $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalSubmitActionLabel('Rejeter')
                    ->modalCancelActionLabel('Annuler')
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label('Motif du rejet')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Article $record, array $data) {
                        $record->update([
                            'status'           => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'reviewed_by'      => auth()->id(),
                            'reviewed_at'      => now(),
                        ]);
                        Notification::make()->title('Article rejeté')->success()->send();
                    }),

                FrenchActions::confirm(
                    Action::make('unpublish')
                        ->label('Dépublier')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('gray')
                        ->visible(fn (Article $record) => $record->status === 'published'),
                    'Dépublier cet article ?',
                    'L\'article repassera en brouillon et ne sera plus visible publiquement.',
                )->action(function (Article $record) {
                    $record->update([
                        'status'       => 'draft',
                        'published_at' => null,
                    ]);
                    Notification::make()->title('Article dépublié')->success()->send();
                }),

                FrenchActions::delete(),

                RestoreAction::make()->label('Restaurer'),
                ForceDeleteAction::make()->label('Supprimer définitivement'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
