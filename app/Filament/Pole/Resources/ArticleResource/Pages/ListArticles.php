<?php

namespace App\Filament\Pole\Resources\ArticleResource\Pages;

use App\Filament\Pole\Resources\ArticleResource;
use Filament\Resources\Pages\ListRecords;

class ListArticles extends ListRecords
{
    protected static string $resource = ArticleResource::class;
}
