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
        $previewQuestions = (int) SiteSetting::get('home_questions_preview', 3);
        $previewArticles  = (int) SiteSetting::get('home_articles_preview', 3);
        $previewEvents    = (int) SiteSetting::get('home_events_preview', 3);

        return [
            'settings'  => SiteSetting::getGroup('home'),
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
