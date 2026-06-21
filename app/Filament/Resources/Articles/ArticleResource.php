<?php

declare(strict_types=1);

namespace App\Filament\Resources\Articles;

use App\Filament\Resources\Articles\Pages\EditArticle;
use App\Filament\Resources\Articles\Pages\ListArticles;
use App\Filament\Resources\Articles\Pages\ViewArticle;
use App\Filament\Resources\Articles\Schemas\ArticleForm;
use App\Filament\Resources\Articles\Schemas\ArticleInfolist;
use App\Filament\Resources\Articles\Tables\ArticlesTable;
use App\Filament\Resources\Concerns\AuthorizesViaPermission;
use App\Models\Article;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArticleResource extends Resource
{
    use AuthorizesViaPermission;

    protected static ?string $model = Article::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Articles';

    protected static ?string $modelLabel = 'Article';

    protected static ?string $pluralModelLabel = 'Articles';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Blog';
    }

    public static function form(Schema $schema): Schema
    {
        return ArticleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ArticleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArticlesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('author');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArticles::route('/'),
            'view'  => ViewArticle::route('/{record}'),
            'edit'  => EditArticle::route('/{record}/edit'),
        ];
    }
}
