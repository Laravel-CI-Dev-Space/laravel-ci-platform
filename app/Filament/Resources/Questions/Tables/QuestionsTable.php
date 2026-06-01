<?php

declare(strict_types=1);

namespace App\Filament\Resources\Questions\Tables;

use App\Models\Question;
use App\Services\Forum\QuestionService;
use Filament\Actions\Action as TableAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('user.avatar')
                    ->label('Auteur')
                    ->circular()
                    ->defaultImageUrl(asset('assets/logo.jpeg')),

                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->limit(60)
                    ->tooltip(fn (Question $record): string => $record->title),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'hidden'    => 'warning',
                        'closed'    => 'gray',
                        'deleted'   => 'danger',
                        default     => 'gray',
                    }),

                ToggleColumn::make('is_pinned')
                    ->label('Épinglé')
                    ->onColor('success')
                    ->offColor('danger'),

                TextColumn::make('votes_score')
                    ->label('Votes')
                    ->sortable(),

                TextColumn::make('answers_count')
                    ->label('Réponses')
                    ->sortable(),

                TextColumn::make('views_count')
                    ->label('Vues')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'published' => 'Publié',
                        'hidden'    => 'Caché',
                        'closed'    => 'Fermé',
                        'deleted'   => 'Supprimé',
                    ]),

                Filter::make('pinned')
                    ->label('Épinglées uniquement')
                    ->query(fn (Builder $q) => $q->where('is_pinned', true)),

                Filter::make('created_from')
                    ->label('Période')
                    ->form([
                        DatePicker::make('from')->label('Du'),
                        DatePicker::make('until')->label('Au'),
                    ])
                    ->query(function (Builder $q, array $data) {
                        return $q
                            ->when($data['from'], fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
                            ->when($data['until'], fn ($q, $v) => $q->whereDate('created_at', '<=', $v));
                    }),
            ])

            ->actions([
                ViewAction::make(),
                EditAction::make(),

                TableAction::make('pin')
                    ->label(fn (Question $record): string => $record->is_pinned ? 'Désépingler' : 'Épingler')
                    ->icon(fn (Question $record): string => $record->is_pinned ? 'heroicon-o-x-mark' : 'heroicon-o-map-pin')
                    ->color(fn (Question $record): string => $record->is_pinned ? 'gray' : 'warning')
                    ->action(fn (Question $record) => app(QuestionService::class)->togglePin($record)),

                TableAction::make('hide')
                    ->label('Cacher')
                    ->icon('heroicon-o-eye-slash')
                    ->color('warning')
                    ->visible(fn (Question $record): bool => $record->status === 'published')
                    ->requiresConfirmation()
                    ->modalHeading('Cacher cette question ?')
                    ->action(fn (Question $record) => $record->update(['status' => 'hidden'])),

                TableAction::make('publish')
                    ->label('Publier')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Question $record): bool => $record->status === 'hidden')
                    ->action(fn (Question $record) => $record->update(['status' => 'published'])),

                DeleteAction::make()
                    ->requiresConfirmation(),
            ])

            ->defaultSort('created_at', 'desc');
    }
}
