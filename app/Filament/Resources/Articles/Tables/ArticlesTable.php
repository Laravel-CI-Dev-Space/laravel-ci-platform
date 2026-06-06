<?php

declare(strict_types=1);

namespace App\Filament\Resources\Articles\Tables;

use App\Models\Article;
use Filament\Actions\Action as TableAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('author.avatar')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(asset('assets/logo.jpeg')),

                TextColumn::make('author.name')
                    ->label('Auteur')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->limit(55)
                    ->tooltip(fn (Article $record): string => $record->title)
                    ->url(fn (Article $record): string => $record->status === 'published'
                        ? route('blog.show', $record->slug)
                        : '#'),

                TextColumn::make('level')
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

                TextColumn::make('status')
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

                TextColumn::make('views_count')
                    ->label('Vues')
                    ->sortable(),

                TextColumn::make('comments_count')
                    ->label('Commentaires')
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label('Publié le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
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
                    ->options([
                        'beginner'     => 'Débutant',
                        'intermediate' => 'Intermédiaire',
                        'advanced'     => 'Avancé',
                    ]),

                Filter::make('pending')
                    ->label('En attente uniquement')
                    ->query(fn (Builder $q) => $q->where('status', 'pending')),

                Filter::make('published_period')
                    ->label('Période de publication')
                    ->form([
                        DatePicker::make('from')->label('Du'),
                        DatePicker::make('until')->label('Au'),
                    ])
                    ->query(function (Builder $q, array $data): Builder {
                        return $q
                            ->when($data['from'] ?? null, fn ($q, $v) => $q->whereDate('published_at', '>=', $v))
                            ->when($data['until'] ?? null, fn ($q, $v) => $q->whereDate('published_at', '<=', $v));
                    }),

                Filter::make('high_traffic')
                    ->label('Populaires (> 100 vues)')
                    ->query(fn (Builder $q) => $q->where('views_count', '>', 100)),
            ])

            ->actions([
                ViewAction::make(),
                EditAction::make(),

                TableAction::make('publish')
                    ->label('Publier')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Article $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Publier cet article ?')
                    ->modalDescription('L\'article sera immédiatement visible par tous les membres.')
                    ->action(function (Article $record): void {
                        $record->update([
                            'status'       => 'published',
                            'published_at' => now(),
                            'reviewed_at'  => now(),
                            'reviewed_by'  => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Article publié avec succès')
                            ->success()
                            ->send();
                    }),

                TableAction::make('reject')
                    ->label('Rejeter')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Article $record): bool => $record->status === 'pending')
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label('Raison du rejet')
                            ->required()
                            ->minLength(10)
                            ->rows(4),
                    ])
                    ->action(function (Article $record, array $data): void {
                        $record->update([
                            'status'           => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'reviewed_at'      => now(),
                            'reviewed_by'      => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Article rejeté')
                            ->warning()
                            ->send();
                    }),

                TableAction::make('unpublish')
                    ->label('Dépublier')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('gray')
                    ->visible(fn (Article $record): bool => $record->status === 'published')
                    ->requiresConfirmation()
                    ->modalHeading('Dépublier cet article ?')
                    ->modalDescription("L'article repassera en brouillon et ne sera plus visible.")
                    ->action(fn (Article $record): bool => $record->update(['status' => 'draft'])),
            ])

            ->defaultSort('created_at', 'desc');
    }
}
