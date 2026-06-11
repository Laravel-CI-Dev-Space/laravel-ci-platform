<?php

declare(strict_types=1);

namespace App\Observers;

use App\Filament\Resources\Articles\ArticleResource;
use App\Models\Article;
use App\Services\NotificationService;

class ArticleObserver
{
    public function __construct(private readonly NotificationService $notifications) {}

    public function created(Article $article): void
    {
        $this->notifications->notifyAdmins('new_article', [
            'message' => "Nouvel article soumis : « {$article->title} » par {$article->author?->name}",
            'url'     => ArticleResource::getUrl('edit', ['record' => $article]),
            'icon'    => 'fa-solid fa-book-open',
        ]);
    }
}
