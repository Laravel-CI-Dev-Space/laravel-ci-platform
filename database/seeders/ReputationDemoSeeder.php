<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Comment;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Profile;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Jeu de données de démonstration pour valider le calcul de réputation :
 * - Membre 1 publie un article et une question
 * - Membre 2 commente l'article et la question de Membre 1
 * - Les deux membres s'inscrivent à un événement
 */
class ReputationDemoSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::updateOrCreate(
            ['email' => 'reputation-demo-author@laravel.ci'],
            [
                'name'              => 'Aya Konaté',
                'github_id'         => 'demo-author-1',
                'github_username'   => 'aya-konate',
                'email_verified_at' => now(),
            ]
        );

        $commenter = User::updateOrCreate(
            ['email' => 'reputation-demo-commenter@laravel.ci'],
            [
                'name'              => 'Bakary Traoré',
                'github_id'         => 'demo-commenter-1',
                'github_username'   => 'bakary-traore',
                'email_verified_at' => now(),
            ]
        );

        foreach ([$author, $commenter] as $user) {
            Profile::firstOrCreate(['user_id' => $user->id]);
        }

        $article = Article::updateOrCreate(
            ['slug' => 'demo-article-reputation'],
            [
                'user_id'      => $author->id,
                'title'        => 'Démo réputation : article',
                'excerpt'      => 'Article de démonstration pour le calcul de réputation.',
                'body'         => 'Contenu de démonstration pour valider le calcul des points de réputation.',
                'level'        => 'beginner',
                'status'       => 'published',
                'published_at' => now(),
            ]
        );

        $question = Question::updateOrCreate(
            ['slug' => 'demo-question-reputation'],
            [
                'user_id' => $author->id,
                'title'   => 'Démo réputation : question',
                'body'    => 'Contenu de démonstration pour valider le calcul des points de réputation.',
                'status'  => 'published',
            ]
        );

        Comment::updateOrCreate(
            [
                'commentable_type' => Article::class,
                'commentable_id'   => $article->id,
                'user_id'          => $commenter->id,
            ],
            ['body' => 'Bravo pour cet article, très clair !']
        );

        Comment::updateOrCreate(
            [
                'commentable_type' => Question::class,
                'commentable_id'   => $question->id,
                'user_id'          => $commenter->id,
            ],
            ['body' => 'Bonne question, je rencontre le même souci.']
        );

        $event = Event::updateOrCreate(
            ['slug' => 'demo-event-reputation'],
            [
                'created_by'  => $author->id,
                'title'       => 'Démo réputation : meetup',
                'description' => 'Événement de démonstration pour valider le calcul de réputation.',
                'type'        => 'meetup',
                'location'    => 'Abidjan',
                'starts_at'   => now()->addWeek(),
                'ends_at'     => now()->addWeek()->addHours(2),
                'status'      => 'published',
            ]
        );

        foreach ([$author, $commenter] as $user) {
            EventRegistration::updateOrCreate(
                ['event_id' => $event->id, 'user_id' => $user->id],
                [
                    'status'        => 'confirmed',
                    'ical_token'    => Str::random(64),
                    'registered_at' => now(),
                ]
            );
        }
    }
}
