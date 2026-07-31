<?php

namespace App\Services\Web;

use App\Models\Article;
use App\Models\Event;
use App\Models\HomeStat;
use App\Models\Partner;
use App\Models\Question;
use App\Models\SiteSetting;

class HomeService
{
    /** Récupère toutes les données pour la page d'accueil. */
    public function getHomeData(): array
    {
        // getGroup() charge toutes les clés 'home.*' en 1 seule requête mise en cache.
        // On réutilise la collection pour extraire les limites - évite 3 get() individuels.
        $homeSettings = SiteSetting::getGroup('home');

        $previewQuestions = (int) ($homeSettings['home_questions_preview'] ?? 3);
        $previewArticles  = (int) ($homeSettings['home_articles_preview'] ?? 3);
        $previewEvents    = (int) ($homeSettings['home_events_preview'] ?? 3);

        return [
            'settings'  => $homeSettings,
            'stats'     => HomeStat::cachedActive(),
            'questions' => Question::with(['user', 'tags'])
                ->published()
                ->withCount('answers')
                ->latest('last_activity_at')
                ->limit($previewQuestions)
                ->get(),
            'articles' => Article::with(['author', 'tags'])
                ->published()
                ->latest('published_at')
                ->limit($previewArticles)
                ->get(),
            'events' => Event::published()
                ->upcoming()
                ->with('creator')
                ->orderBy('starts_at')
                ->limit($previewEvents)
                ->get(),
            'partners' => Partner::cachedActive(),
        ];
    }

    /** Récupère les settings globaux du site. */
    public function getGlobalSettings(): array
    {
        return [
            'general' => SiteSetting::getGroup('general'),
            'social'  => SiteSetting::getGroup('social'),
            'seo'     => SiteSetting::getGroup('seo'),
        ];
    }
}
