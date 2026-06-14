<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Question;
use App\Models\Report;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Temporary seeder to QA the QuestionResource, ArticleResource and
 * ReportResource Filament admin pages. Safe to delete after testing.
 */
class QaTestContentSeeder extends Seeder
{
    public function run(): void
    {
        $member = User::where('email', 'member@laravelci.com')->first();
        $admin  = User::where('email', 'admin@laravelci.com')->first();

        if (! $member || ! $admin) {
            $this->command->warn('member@laravelci.com / admin@laravelci.com introuvables — lancez UserSeeder avant.');

            return;
        }

        $laravelTag  = Tag::where('name', 'Laravel')->first();
        $eloquentTag = Tag::where('name', 'Eloquent')->first();

        // Questions
        $question1 = Question::create([
            'user_id'          => $member->id,
            'title'            => 'Comment optimiser une requête Eloquent avec N+1 ?',
            'slug'             => 'comment-optimiser-une-requete-eloquent-avec-n-plus-1',
            'body'             => "J'ai un problème de performance sur ma liste d'articles, **comment éviter le N+1** ?",
            'body_html'        => "<p>J'ai un problème de performance sur ma liste d'articles, <strong>comment éviter le N+1</strong> ?</p>",
            'status'           => 'published',
            'is_pinned'        => false,
            'votes_score'      => 5,
            'answers_count'    => 2,
            'comments_count'   => 1,
            'last_activity_at' => now()->subHours(2),
        ]);

        $question2 = Question::create([
            'user_id'          => $member->id,
            'title'            => 'Question test à modérer (épinglage / suppression)',
            'slug'             => 'question-test-a-moderer',
            'body'             => 'Contenu de test pour vérifier les actions de modération.',
            'body_html'        => '<p>Contenu de test pour vérifier les actions de modération.</p>',
            'status'           => 'published',
            'is_pinned'        => false,
            'last_activity_at' => now()->subDay(),
        ]);

        if ($laravelTag && $eloquentTag) {
            $question1->tags()->attach([$laravelTag->id, $eloquentTag->id]);
        }

        // Articles
        $article1 = Article::create([
            'user_id'   => $member->id,
            'title'     => 'Les bonnes pratiques Laravel en 2026',
            'slug'      => 'les-bonnes-pratiques-laravel-en-2026',
            'excerpt'   => 'Un tour des bonnes pratiques pour écrire du code Laravel propre et maintenable.',
            'body'      => "## Introduction\n\nVoici un **article de test** en attente de validation.",
            'body_html' => '<h2>Introduction</h2><p>Voici un <strong>article de test</strong> en attente de validation.</p>',
            'level'     => 'intermediate',
            'status'    => 'pending',
        ]);

        $article2 = Article::create([
            'user_id'      => $member->id,
            'reviewed_by'  => $admin->id,
            'title'        => 'Article déjà publié (test dépublication)',
            'slug'         => 'article-deja-publie-test',
            'excerpt'      => 'Article de test déjà publié.',
            'body'         => 'Contenu déjà publié.',
            'body_html'    => '<p>Contenu déjà publié.</p>',
            'level'        => 'beginner',
            'status'       => 'published',
            'published_at' => now()->subDays(3),
            'reviewed_at'  => now()->subDays(3),
        ]);

        if ($laravelTag) {
            $article1->tags()->attach([$laravelTag->id]);
        }

        // Reports
        Report::create([
            'reporter_id'     => $admin->id,
            'reportable_id'   => $question2->id,
            'reportable_type' => Question::class,
            'reason'          => 'spam',
            'description'     => 'Ce message ressemble à du spam, à vérifier.',
            'status'          => 'pending',
        ]);

        Report::create([
            'reporter_id'     => $member->id,
            'reportable_id'   => $article1->id,
            'reportable_type' => Article::class,
            'reason'          => 'inappropriate',
            'description'     => 'Contenu jugé inapproprié par un membre.',
            'status'          => 'pending',
        ]);

        Report::create([
            'reporter_id'     => $member->id,
            'reportable_id'   => $article2->id,
            'reportable_type' => Article::class,
            'reason'          => 'other',
            'description'     => 'Signalement déjà traité (test affichage).',
            'status'          => 'resolved',
            'handled_by'      => $admin->id,
            'admin_note'      => 'Vérifié, rien à signaler.',
            'handled_at'      => now()->subDay(),
        ]);

        $this->command->info('QA content seeded: 2 questions, 2 articles, 3 reports.');
    }
}
