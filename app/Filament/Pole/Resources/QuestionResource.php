<?php

namespace App\Filament\Pole\Resources;

use App\Enums\QuestionStatus;
use App\Enums\UserRole;
use App\Models\Question;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'Forum — Questions';

    protected static ?string $modelLabel = 'Question';

    protected static ?string $pluralModelLabel = 'Questions';

    

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(UserRole::PoleTechFormation->value) ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Question')
                    ->searchable()
                    ->limit(55),

                TextColumn::make('user.name')
                    ->label('Auteur')
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (QuestionStatus $state) => match ($state) {
                        QuestionStatus::Published => 'success',
                        QuestionStatus::Hidden    => 'warning',
                        QuestionStatus::Deleted   => 'danger',
                        default                   => 'gray',
                    })
                    ->formatStateUsing(fn (QuestionStatus $state) => match ($state) {
                        QuestionStatus::Published => 'Publiée',
                        QuestionStatus::Hidden    => 'Masquée',
                        QuestionStatus::Closed    => 'Fermée',
                        QuestionStatus::Deleted   => 'Supprimée',
                    }),

                IconColumn::make('is_pinned')
                    ->label('Épinglée')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedBookmark)
                    ->falseIcon(Heroicon::OutlinedBookmark),

                TextColumn::make('votes_count')
                    ->label('Votes')
                    ->counts('votes')
                    ->badge()
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label('Posée le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        QuestionStatus::Published->value => 'Publiée',
                        QuestionStatus::Hidden->value    => 'Masquée',
                        QuestionStatus::Closed->value    => 'Fermée',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('pin')
                    ->label('Épingler')
                    ->icon(Heroicon::OutlinedBookmark)
                    ->color('info')
                    ->visible(fn (Question $record) => ! $record->is_pinned)
                    ->action(fn (Question $record) => $record->update(['is_pinned' => true])),
                Action::make('unpin')
                    ->label('Désépingler')
                    ->icon(Heroicon::OutlinedBookmarkSlash)
                    ->color('gray')
                    ->visible(fn (Question $record) => $record->is_pinned)
                    ->action(fn (Question $record) => $record->update(['is_pinned' => false])),
                Action::make('hide')
                    ->label('Masquer')
                    ->icon(Heroicon::OutlinedEyeSlash)
                    ->color('warning')
                    ->visible(fn (Question $record) => $record->status === QuestionStatus::Published)
                    ->action(fn (Question $record) => $record->update(['status' => QuestionStatus::Hidden]))
                    ->requiresConfirmation(),
                Action::make('restore')
                    ->label('Restaurer')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('success')
                    ->visible(fn (Question $record) => $record->status === QuestionStatus::Hidden)
                    ->action(fn (Question $record) => $record->update(['status' => QuestionStatus::Published])),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Pole\Resources\QuestionResource\Pages\ListQuestions::route('/'),
        ];
    }
}
