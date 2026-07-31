<?php

namespace App\Filament\Pole\Resources;

use App\Enums\ArticleStatus;
use App\Enums\UserRole;
use App\Models\Article;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?string $navigationLabel = 'Articles';

    protected static ?string $modelLabel = 'Article';

    protected static ?string $pluralModelLabel = 'Articles';

    

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(UserRole::PoleCommunication->value) ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Titre')
                ->required()
                ->maxLength(255),

            Textarea::make('excerpt')
                ->label('Résumé')
                ->rows(3)
                ->maxLength(500),

            RichEditor::make('body')
                ->label('Contenu')
                ->required()
                ->columnSpanFull(),

            Select::make('status')
                ->label('Statut')
                ->options([
                    ArticleStatus::Draft->value     => 'Brouillon',
                    ArticleStatus::Pending->value   => 'En attente de relecture',
                    ArticleStatus::Published->value => 'Publié',
                ])
                ->required()
                ->default(ArticleStatus::Draft->value),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('user.name')
                    ->label('Auteur')
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (ArticleStatus $state) => match ($state) {
                        ArticleStatus::Published => 'success',
                        ArticleStatus::Pending   => 'warning',
                        ArticleStatus::Rejected  => 'danger',
                        default                  => 'gray',
                    })
                    ->formatStateUsing(fn (ArticleStatus $state) => match ($state) {
                        ArticleStatus::Published => 'Publié',
                        ArticleStatus::Pending   => 'En attente',
                        ArticleStatus::Draft     => 'Brouillon',
                        ArticleStatus::Rejected  => 'Rejeté',
                    }),

                TextColumn::make('published_at')
                    ->label('Publié le')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        ArticleStatus::Draft->value     => 'Brouillon',
                        ArticleStatus::Pending->value   => 'En attente',
                        ArticleStatus::Published->value => 'Publié',
                        ArticleStatus::Rejected->value  => 'Rejeté',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('publish')
                    ->label('Publier')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Article $record) => $record->status !== ArticleStatus::Published)
                    ->action(function (Article $record) {
                        $record->update([
                            'status'       => ArticleStatus::Published,
                            'published_at' => now(),
                        ]);
                    })
                    ->requiresConfirmation(),
                Action::make('unpublish')
                    ->label('Dépublier')
                    ->icon(Heroicon::OutlinedArchiveBox)
                    ->color('warning')
                    ->visible(fn (Article $record) => $record->status === ArticleStatus::Published)
                    ->action(fn (Article $record) => $record->update(['status' => ArticleStatus::Draft]))
                    ->requiresConfirmation(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Pole\Resources\ArticleResource\Pages\ListArticles::route('/'),
            'create' => \App\Filament\Pole\Resources\ArticleResource\Pages\CreateArticle::route('/create'),
            'edit'   => \App\Filament\Pole\Resources\ArticleResource\Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
