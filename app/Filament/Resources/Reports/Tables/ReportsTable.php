<?php

namespace App\Filament\Resources\Reports\Tables;

use App\Filament\Resources\Reports\ReportResource;
use App\Filament\Support\FrenchActions;
use App\Models\Answer;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Question;
use App\Models\Report;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reportable_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Question::class => 'Question',
                        Article::class  => 'Article',
                        Comment::class  => 'Commentaire',
                        Answer::class   => 'Réponse',
                        default         => class_basename($state),
                    }),

                TextColumn::make('reportable')
                    ->label('Contenu signalé')
                    ->state(fn (Report $record): string => match (true) {
                        $record->reportable instanceof Question, $record->reportable instanceof Article => Str::limit($record->reportable->title, 50),
                        $record->reportable instanceof Comment, $record->reportable instanceof Answer   => Str::limit($record->reportable->body, 50),
                        default                                                                         => '— (supprimé)',
                    }),

                TextColumn::make('reporter.name')
                    ->label('Signalé par')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('reason')
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
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'harassment'                      => 'danger',
                        'inappropriate', 'misinformation' => 'warning',
                        default                           => 'gray',
                    }),

                TextColumn::make('status')
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

                TextColumn::make('handler.name')
                    ->label('Traité par')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('handled_at')
                    ->label('Traité le')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Signalé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'  => 'En attente',
                        'reviewed' => 'Examiné',
                        'resolved' => 'Résolu',
                        'rejected' => 'Rejeté',
                    ]),

                SelectFilter::make('reason')
                    ->label('Motif')
                    ->options([
                        'spam'           => 'Spam',
                        'inappropriate'  => 'Contenu inapproprié',
                        'harassment'     => 'Harcèlement',
                        'misinformation' => 'Désinformation',
                        'copyright'      => 'Droit d\'auteur',
                        'other'          => 'Autre',
                    ]),

                SelectFilter::make('reportable_type')
                    ->label('Type de contenu')
                    ->options([
                        Question::class => 'Question',
                        Article::class  => 'Article',
                        Comment::class  => 'Commentaire',
                        Answer::class   => 'Réponse',
                    ]),
            ])
            ->actions([
                FrenchActions::viewPage(ReportResource::class),

                FrenchActions::confirm(
                    Action::make('review')
                        ->label('Marquer comme examiné')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->visible(fn (Report $record) => $record->status === 'pending'),
                    'Marquer ce signalement comme examiné ?',
                )->action(function (Report $record) {
                    $record->update([
                        'status'     => 'reviewed',
                        'handled_by' => auth()->id(),
                        'handled_at' => now(),
                    ]);
                    Notification::make()->title('Signalement marqué comme examiné')->success()->send();
                }),

                Action::make('resolve')
                    ->label('Résoudre')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Report $record) => in_array($record->status, ['pending', 'reviewed']))
                    ->requiresConfirmation()
                    ->modalSubmitActionLabel('Confirmer')
                    ->modalCancelActionLabel('Annuler')
                    ->schema([
                        Textarea::make('admin_note')
                            ->label('Note interne (optionnel)')
                            ->rows(3),
                    ])
                    ->action(function (Report $record, array $data) {
                        $record->update([
                            'status'     => 'resolved',
                            'admin_note' => $data['admin_note'] ?: null,
                            'handled_by' => auth()->id(),
                            'handled_at' => now(),
                        ]);
                        Notification::make()->title('Signalement résolu')->success()->send();
                    }),

                Action::make('dismiss')
                    ->label('Rejeter le signalement')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Report $record) => in_array($record->status, ['pending', 'reviewed']))
                    ->requiresConfirmation()
                    ->modalSubmitActionLabel('Confirmer')
                    ->modalCancelActionLabel('Annuler')
                    ->schema([
                        Textarea::make('admin_note')
                            ->label('Note interne (optionnel)')
                            ->rows(3),
                    ])
                    ->action(function (Report $record, array $data) {
                        $record->update([
                            'status'     => 'rejected',
                            'admin_note' => $data['admin_note'] ?: null,
                            'handled_by' => auth()->id(),
                            'handled_at' => now(),
                        ]);
                        Notification::make()->title('Signalement rejeté')->success()->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
