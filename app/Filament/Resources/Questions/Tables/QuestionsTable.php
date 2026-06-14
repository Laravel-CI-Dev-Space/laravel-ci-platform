<?php

namespace App\Filament\Resources\Questions\Tables;

use App\Filament\Resources\Questions\QuestionResource;
use App\Filament\Support\FrenchActions;
use App\Models\Question;
use Filament\Actions\Action;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class QuestionsTable
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

                TextColumn::make('user.name')
                    ->label('Auteur')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
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

                IconColumn::make('is_pinned')
                    ->label('Épinglée')
                    ->boolean(),

                TextColumn::make('votes_score')
                    ->label('Votes')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('answers_count')
                    ->label('Réponses')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('comments_count')
                    ->label('Commentaires')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('last_activity_at')
                    ->label('Dernière activité')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'published' => 'Publiée',
                        'hidden'    => 'Masquée',
                        'closed'    => 'Clôturée',
                        'deleted'   => 'Supprimée',
                    ]),

                TernaryFilter::make('is_pinned')
                    ->label('Épinglées'),

                TrashedFilter::make(),
            ])
            ->actions([
                FrenchActions::viewPage(QuestionResource::class),

                Action::make('togglePin')
                    ->label(fn (Question $record) => $record->is_pinned ? 'Désépingler' : 'Épingler')
                    ->icon(fn (Question $record) => $record->is_pinned ? 'heroicon-o-bookmark-slash' : 'heroicon-o-bookmark')
                    ->color('warning')
                    ->action(fn (Question $record) => $record->update(['is_pinned' => ! $record->is_pinned])),

                FrenchActions::confirm(
                    Action::make('hide')
                        ->label('Masquer')
                        ->icon('heroicon-o-eye-slash')
                        ->color('gray')
                        ->visible(fn (Question $record) => $record->status === 'published'),
                    'Masquer cette question ?',
                    'La question ne sera plus visible publiquement.',
                )->action(function (Question $record) {
                    $record->update(['status' => 'hidden']);
                    Notification::make()->title('Question masquée')->success()->send();
                }),

                FrenchActions::confirm(
                    Action::make('close')
                        ->label('Clôturer')
                        ->icon('heroicon-o-lock-closed')
                        ->color('info')
                        ->visible(fn (Question $record) => $record->status === 'published'),
                    'Clôturer cette question ?',
                    'Plus aucune nouvelle réponse ne pourra être ajoutée.',
                )->action(function (Question $record) {
                    $record->update(['status' => 'closed']);
                    Notification::make()->title('Question clôturée')->success()->send();
                }),

                Action::make('republish')
                    ->label('Republier')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->visible(fn (Question $record) => in_array($record->status, ['hidden', 'closed']))
                    ->action(function (Question $record) {
                        $record->update(['status' => 'published']);
                        Notification::make()->title('Question republiée')->success()->send();
                    }),

                FrenchActions::delete(),

                RestoreAction::make()->label('Restaurer'),
                ForceDeleteAction::make()->label('Supprimer définitivement'),
            ])
            ->defaultSort('last_activity_at', 'desc');
    }
}
