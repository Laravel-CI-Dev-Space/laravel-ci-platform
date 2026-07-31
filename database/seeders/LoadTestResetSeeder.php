<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Supprime toutes les données générées par LoadTestSeeder.
 * Identifie les lignes load-test par les discriminateurs injectés lors du seed.
 *
 * Usage : php artisan db:seed --class=LoadTestResetSeeder
 */
class LoadTestResetSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('  Load Test Reset — suppression des données de test...');
        $this->command->info('');

        // ── Récupère les IDs des utilisateurs load-test ────────────────────────
        $userIds = DB::table('users')
            ->where('github_id', 'like', 'LT%')
            ->pluck('id')
            ->toArray();

        if (empty($userIds)) {
            $this->command->warn('  Aucune donnée load-test trouvée (github_id LIKE "LT%").');
            return;
        }

        $this->command->info('  ' . count($userIds) . ' utilisateurs load-test identifiés.');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Votes des users LT
        $deleted = DB::table('votes')->whereIn('user_id', $userIds)->delete();
        $this->command->line("  votes supprimés           : {$deleted}");

        // Commentaires des users LT
        $deleted = DB::table('comments')->whereIn('user_id', $userIds)->delete();
        $this->command->line("  comments supprimés        : {$deleted}");

        // Réponses des users LT
        $deleted = DB::table('answers')->whereIn('user_id', $userIds)->delete();
        $this->command->line("  answers supprimés         : {$deleted}");

        // Candidatures et favoris
        $deleted = DB::table('job_applications')->whereIn('user_id', $userIds)->delete();
        $this->command->line("  job_applications          : {$deleted}");

        $deleted = DB::table('job_favorites')->whereIn('user_id', $userIds)->delete();
        $this->command->line("  job_favorites             : {$deleted}");

        // Inscriptions événements
        $deleted = DB::table('event_registrations')->whereIn('user_id', $userIds)->delete();
        $this->command->line("  event_registrations       : {$deleted}");

        // Cartes membres
        $deleted = DB::table('member_cards')->whereIn('user_id', $userIds)->delete();
        $this->command->line("  member_cards              : {$deleted}");

        // Rôles
        $deleted = DB::table('model_has_roles')
            ->where('model_type', 'App\\Models\\User')
            ->whereIn('model_id', $userIds)
            ->delete();
        $this->command->line("  model_has_roles           : {$deleted}");

        // Profils
        $deleted = DB::table('profiles')->whereIn('user_id', $userIds)->delete();
        $this->command->line("  profiles                  : {$deleted}");

        // Questions LT (slug like '-lt%')
        $ltQuestionIds = DB::table('questions')->where('slug', 'like', '%-lt%')->pluck('id');
        DB::table('question_tag')->whereIn('question_id', $ltQuestionIds)->delete();
        DB::table('votes')->where('votable_type', 'App\\Models\\Question')->whereIn('votable_id', $ltQuestionIds)->delete();
        DB::table('comments')->where('commentable_type', 'App\\Models\\Question')->whereIn('commentable_id', $ltQuestionIds)->delete();
        DB::table('answers')->whereIn('question_id', $ltQuestionIds)->delete();
        $deleted = DB::table('questions')->where('slug', 'like', '%-lt%')->delete();
        $this->command->line("  questions supprimées      : {$deleted}");

        // Articles LT
        $ltArticleIds = DB::table('articles')->where('slug', 'like', '%-lt%')->pluck('id');
        DB::table('article_tag')->whereIn('article_id', $ltArticleIds)->delete();
        DB::table('comments')->where('commentable_type', 'App\\Models\\Article')->whereIn('commentable_id', $ltArticleIds)->delete();
        $deleted = DB::table('articles')->where('slug', 'like', '%-lt%')->delete();
        $this->command->line("  articles supprimés        : {$deleted}");

        // Événements LT
        $ltEventIds = DB::table('events')->where('slug', 'like', '%-lt%')->pluck('id');
        DB::table('event_registrations')->whereIn('event_id', $ltEventIds)->delete();
        $deleted = DB::table('events')->where('slug', 'like', '%-lt%')->delete();
        $this->command->line("  events supprimés          : {$deleted}");

        // Offres LT (slug contient 'lt')
        $ltJobIds = DB::table('job_offers')->where('title', 'like', '% LT%')->pluck('id');
        DB::table('job_applications')->whereIn('job_offer_id', $ltJobIds)->delete();
        DB::table('job_favorites')->whereIn('job_offer_id', $ltJobIds)->delete();
        $deleted = DB::table('job_offers')->where('title', 'like', '% LT%')->delete();
        $this->command->line("  job_offers supprimées     : {$deleted}");

        // Entreprises LT
        $deleted = DB::table('companies')->where('name', 'like', '% LT%')->delete();
        $this->command->line("  companies supprimées      : {$deleted}");

        // Newsletter LT
        $deleted = DB::table('newsletter_subscribers')->where('email', 'like', 'lt%_%')->delete();
        $this->command->line("  newsletter_subscribers    : {$deleted}");

        // Analytics LT
        $deleted = DB::table('analytics_page_views')->where('browser', 'LoadTest')->delete();
        $this->command->line("  analytics_page_views      : {$deleted}");

        // Utilisateurs LT
        $deleted = DB::table('users')->whereIn('id', $userIds)->delete();
        $this->command->line("  users supprimés           : {$deleted}");

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('');
        $this->command->info('  Reset terminé. La base est propre.');
        $this->command->info('');
    }
}
