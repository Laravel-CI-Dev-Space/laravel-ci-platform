<?php

namespace App\Providers;

use App\Models\Answer;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Question;
use App\Observers\AnswerObserver;
use App\Observers\ArticleObserver;
use App\Observers\CommentObserver;
use App\Observers\QuestionObserver;
use App\View\Composers\GlobalSettingsComposer;
use App\View\Composers\SidebarBadgeComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Injecte $globalSettings dans le layout web, le header et le footer.
        View::composer(
            ['layouts.web', 'components.web.header', 'components.web.footer'],
            GlobalSettingsComposer::class
        );

        // Badge counts dans la sidebar membre du dashboard.
        View::composer('components.dashboard.sidebar', SidebarBadgeComposer::class);

        // Notifie les admins/super-admins de toute nouvelle activité du site.
        Article::observe(ArticleObserver::class);
        Question::observe(QuestionObserver::class);
        Answer::observe(AnswerObserver::class);
        Comment::observe(CommentObserver::class);
    }
}
