<?php

declare(strict_types=1);

namespace Database\Seeders;

use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seeder de montée en charge — génère des données massives sans passer par Eloquent.
 * Les observers, Scout et les events sont contournés via DB::table() direct.
 *
 * Usage   : php artisan db:seed --class=LoadTestSeeder
 * Reset   : php artisan db:seed --class=LoadTestResetSeeder
 */
class LoadTestSeeder extends Seeder
{
    private const int USERS       = 5_000;
    private const int QUESTIONS   = 2_000;
    private const int ARTICLES    = 500;
    private const int EVENTS      = 50;
    private const int JOB_OFFERS  = 200;
    private const int COMPANIES   = 50;
    private const int NEWSLETTER  = 3_000;
    private const int BATCH       = 500;

    private \Faker\Generator $faker;

    public function run(): void
    {
        $this->faker = FakerFactory::create('fr_FR');

        $this->command->info('');
        $this->command->info('  Load Test Seeder');
        $this->command->info('  Génération de données massives en cours...');
        $this->command->info('');

        // Auto-nettoyage si un run précédent a été interrompu
        $existing = DB::table('users')->where('github_id', 'like', 'LT%')->count();
        if ($existing > 0) {
            $this->command->warn("  Données load-test résiduelles ({$existing} users) — nettoyage automatique...");
            $this->call(LoadTestResetSeeder::class);
            $this->command->info('');
        }

        $memberRoleId = DB::table('roles')->where('name', 'member')->value('id');
        $grades       = DB::table('grades')->orderBy('min_points')->pluck('id')->toArray();
        $forumTagIds  = DB::table('tags')->whereIn('scope', ['forum', 'both'])->pluck('id')->toArray();
        $blogTagIds   = DB::table('tags')->whereIn('scope', ['blog', 'both'])->pluck('id')->toArray();

        // ── 1. Utilisateurs ────────────────────────────────────────────────────
        $this->command->info('  [1/9] Création de ' . number_format(self::USERS) . ' utilisateurs...');

        $countries = ["Côte d'Ivoire", 'Sénégal', 'Cameroun', 'Mali', 'Burkina Faso', 'Togo', 'Bénin', 'Guinée', 'France', 'Canada'];
        $laravelLevels  = ['debutant', 'intermediaire', 'avance', 'expert', 'maitre'];
        $jobStatuses    = ['en_fonction', 'etudiant', 'entrepreneur', 'recherche_emploi', 'freelance'];
        $academicLevels = ['bts', 'licence', 'master_ingenieur', 'doctorat'];

        $maxUserIdBefore = (int) DB::table('users')->max('id');

        $userRows    = [];
        $profileRows = [];
        $roleRows    = [];

        for ($i = 0; $i < self::USERS; $i++) {
            $points    = random_int(0, 8000);
            $gradeIdx  = $grades ? min((int) floor($points / (8000 / count($grades))), count($grades) - 1) : null;
            $createdAt = now()->subDays(random_int(1, 730))->format('Y-m-d H:i:s');

            $userRows[] = [
                'name'              => $this->faker->name(),
                'email'             => 'lt' . $i . '_' . $this->faker->unique()->safeEmail(),
                'github_id'         => 'LT' . str_pad((string) $i, 9, '0', STR_PAD_LEFT),
                'github_username'   => 'lt_user_' . $i,
                'is_active'         => random_int(0, 9) > 0,
                'email_verified_at' => $createdAt,
                'last_login_at'     => now()->subDays(random_int(0, 60))->format('Y-m-d H:i:s'),
                'created_at'        => $createdAt,
                'updated_at'        => $createdAt,
            ];

            $profileRows[] = [
                'points'          => $points,
                'grade_id'        => $grades ? $grades[$gradeIdx] : null,
                'country'         => $countries[array_rand($countries)],
                'city'            => $this->faker->city(),
                'laravel_level'   => $laravelLevels[array_rand($laravelLevels)],
                'job_status'      => $jobStatuses[array_rand($jobStatuses)],
                'academic_level'  => $academicLevels[array_rand($academicLevels)],
                'created_at'      => $createdAt,
                'updated_at'      => $createdAt,
            ];
        }

        foreach (array_chunk($userRows, self::BATCH) as $chunk) {
            DB::table('users')->insert($chunk);
        }

        $userIds = DB::table('users')
            ->where('id', '>', $maxUserIdBefore)
            ->pluck('id')
            ->toArray();

        // Profils : associer le user_id maintenant que les IDs sont connus
        foreach ($userIds as $idx => $uid) {
            $profileRows[$idx]['user_id'] = $uid;
        }
        foreach (array_chunk($profileRows, self::BATCH) as $chunk) {
            DB::table('profiles')->insert($chunk);
        }

        // Rôle 'member'
        if ($memberRoleId) {
            foreach ($userIds as $uid) {
                $roleRows[] = ['model_type' => 'App\\Models\\User', 'model_id' => $uid, 'role_id' => $memberRoleId];
            }
            foreach (array_chunk($roleRows, 1000) as $chunk) {
                DB::table('model_has_roles')->insertOrIgnore($chunk);
            }
        }

        $this->command->info('     ' . count($userIds) . ' utilisateurs créés.');

        // ── 2. Questions forum ─────────────────────────────────────────────────
        $this->command->info('  [2/9] Création de ' . number_format(self::QUESTIONS) . ' questions...');

        $qStatuses  = ['published', 'published', 'published', 'hidden', 'closed'];
        $maxQid     = (int) DB::table('questions')->max('id');
        $questionRows = [];

        for ($i = 0; $i < self::QUESTIONS; $i++) {
            $title     = $this->faker->sentence(random_int(6, 12));
            $body      = implode("\n\n", $this->faker->paragraphs(random_int(2, 5)));
            $createdAt = now()->subDays(random_int(1, 730))->format('Y-m-d H:i:s');
            $status    = $qStatuses[array_rand($qStatuses)];

            $questionRows[] = [
                'user_id'          => $userIds[array_rand($userIds)],
                'title'            => mb_substr($title, 0, 290),
                'slug'             => Str::slug(mb_substr($title, 0, 200)) . '-lt' . ($i + 1),
                'body'             => $body,
                'body_html'        => '<p>' . nl2br(e($body)) . '</p>',
                'status'           => $status,
                'is_pinned'        => false,
                'views_count'      => random_int(0, 3000),
                'votes_score'      => random_int(-10, 80),
                'answers_count'    => 0,
                'comments_count'   => 0,
                'last_activity_at' => $createdAt,
                'created_at'       => $createdAt,
                'updated_at'       => $createdAt,
            ];
        }

        foreach (array_chunk($questionRows, self::BATCH) as $chunk) {
            DB::table('questions')->insert($chunk);
        }

        $questionIds = DB::table('questions')->where('id', '>', $maxQid)->pluck('id')->toArray();

        // Tags des questions
        if ($forumTagIds) {
            $qtRows = [];
            foreach ($questionIds as $qId) {
                $shuffled = $forumTagIds;
                shuffle($shuffled);
                foreach (array_slice($shuffled, 0, random_int(1, min(3, count($forumTagIds)))) as $tagId) {
                    $qtRows[] = ['question_id' => $qId, 'tag_id' => $tagId];
                }
            }
            foreach (array_chunk($qtRows, 1000) as $chunk) {
                DB::table('question_tag')->insertOrIgnore($chunk);
            }
        }

        // Réponses
        $answerRows = [];
        $answerCountMap = [];

        foreach ($questionIds as $qId) {
            $nb = random_int(0, 8);
            $answerCountMap[$qId] = $nb;
            for ($a = 0; $a < $nb; $a++) {
                $body = implode("\n\n", $this->faker->paragraphs(random_int(1, 3)));
                $answerRows[] = [
                    'question_id' => $qId,
                    'user_id'     => $userIds[array_rand($userIds)],
                    'body'        => $body,
                    'body_html'   => '<p>' . nl2br(e($body)) . '</p>',
                    'is_accepted' => false,
                    'created_at'  => now()->subDays(random_int(1, 700))->format('Y-m-d H:i:s'),
                    'updated_at'  => now()->format('Y-m-d H:i:s'),
                ];
            }
        }

        foreach (array_chunk($answerRows, self::BATCH) as $chunk) {
            DB::table('answers')->insert($chunk);
        }

        // Mettre à jour answers_count
        foreach ($answerCountMap as $qId => $count) {
            if ($count > 0) {
                DB::table('questions')->where('id', $qId)->update(['answers_count' => $count]);
            }
        }

        // Votes sur les questions (1 vote/user/question max grâce à insertOrIgnore)
        $voteRows   = [];
        $voteValues = [1, 1, 1, -1];
        $subsetQids = array_slice($questionIds, 0, 1200);

        foreach ($subsetQids as $qId) {
            $nb = random_int(1, 40);
            $voters = array_rand(array_flip($userIds), min($nb, count($userIds)));
            foreach ((array) $voters as $uid) {
                $voteRows[] = [
                    'votable_type' => 'App\\Models\\Question',
                    'votable_id'   => $qId,
                    'user_id'      => $uid,
                    'value'        => $voteValues[array_rand($voteValues)],
                    'created_at'   => now()->subDays(random_int(1, 700))->format('Y-m-d H:i:s'),
                    'updated_at'   => now()->format('Y-m-d H:i:s'),
                ];
            }
        }

        // Commentaires sur les questions
        $commentRows = [];
        foreach (array_slice($questionIds, 0, 1000) as $qId) {
            $nb = random_int(0, 5);
            for ($c = 0; $c < $nb; $c++) {
                $commentRows[] = [
                    'commentable_type' => 'App\\Models\\Question',
                    'commentable_id'   => $qId,
                    'user_id'          => $userIds[array_rand($userIds)],
                    'body'             => $this->faker->paragraph(),
                    'is_hidden'        => false,
                    'created_at'       => now()->subDays(random_int(1, 700))->format('Y-m-d H:i:s'),
                    'updated_at'       => now()->format('Y-m-d H:i:s'),
                ];
            }
        }

        $this->command->info('     ' . count($questionIds) . ' questions, ' . count($answerRows) . ' réponses, ' . count($commentRows) . ' commentaires, ' . count($voteRows) . ' votes.');

        // ── 3. Articles blog ───────────────────────────────────────────────────
        $this->command->info('  [3/9] Création de ' . number_format(self::ARTICLES) . ' articles...');

        $artLevels   = ['beginner', 'intermediate', 'advanced'];
        $maxArtId    = (int) DB::table('articles')->max('id');
        $articleRows = [];

        for ($i = 0; $i < self::ARTICLES; $i++) {
            $title       = $this->faker->sentence(random_int(5, 10));
            $body        = implode("\n\n", $this->faker->paragraphs(random_int(5, 12)));
            $publishedAt = now()->subDays(random_int(1, 730))->format('Y-m-d H:i:s');

            $articleRows[] = [
                'user_id'         => $userIds[array_rand($userIds)],
                'title'           => mb_substr($title, 0, 290),
                'slug'            => Str::slug(mb_substr($title, 0, 200)) . '-lt' . ($i + 1),
                'excerpt'         => $this->faker->paragraph(),
                'body'            => $body,
                'body_html'       => '<p>' . nl2br(e($body)) . '</p>',
                'level'           => $artLevels[array_rand($artLevels)],
                'status'          => 'published',
                'views_count'     => random_int(0, 8000),
                'comments_count'  => 0,
                'newsletter_sent' => false,
                'published_at'    => $publishedAt,
                'created_at'      => $publishedAt,
                'updated_at'      => $publishedAt,
            ];
        }

        foreach (array_chunk($articleRows, self::BATCH) as $chunk) {
            DB::table('articles')->insert($chunk);
        }

        $articleIds = DB::table('articles')->where('id', '>', $maxArtId)->pluck('id')->toArray();

        // Tags des articles
        if ($blogTagIds) {
            $atRows = [];
            foreach ($articleIds as $aId) {
                $shuffled = $blogTagIds;
                shuffle($shuffled);
                foreach (array_slice($shuffled, 0, random_int(1, min(3, count($blogTagIds)))) as $tagId) {
                    $atRows[] = ['article_id' => $aId, 'tag_id' => $tagId];
                }
            }
            foreach (array_chunk($atRows, 1000) as $chunk) {
                DB::table('article_tag')->insertOrIgnore($chunk);
            }
        }

        // Commentaires sur les articles
        foreach (array_slice($articleIds, 0, 400) as $aId) {
            $nb = random_int(0, 10);
            for ($c = 0; $c < $nb; $c++) {
                $commentRows[] = [
                    'commentable_type' => 'App\\Models\\Article',
                    'commentable_id'   => $aId,
                    'user_id'          => $userIds[array_rand($userIds)],
                    'body'             => $this->faker->paragraph(),
                    'is_hidden'        => false,
                    'created_at'       => now()->subDays(random_int(1, 700))->format('Y-m-d H:i:s'),
                    'updated_at'       => now()->format('Y-m-d H:i:s'),
                ];
            }
        }

        // Flush votes + commentaires
        foreach (array_chunk($voteRows, 1000) as $chunk) {
            DB::table('votes')->insertOrIgnore($chunk);
        }
        foreach (array_chunk($commentRows, 1000) as $chunk) {
            DB::table('comments')->insert($chunk);
        }

        $this->command->info('     ' . count($articleIds) . ' articles, ' . count($commentRows) . ' commentaires total.');

        // ── 4. Événements ─────────────────────────────────────────────────────
        $this->command->info('  [4/9] Création de ' . number_format(self::EVENTS) . ' événements...');

        $eventTypes  = ['meetup', 'webinar', 'hackathon', 'conference', 'workshop'];
        $maxEvtId    = (int) DB::table('events')->max('id');
        $eventRows   = [];

        for ($i = 0; $i < self::EVENTS; $i++) {
            $isPast    = $i < 35;
            $startsAt  = $isPast
                ? now()->subDays(random_int(30, 365))
                : now()->addDays(random_int(7, 120));
            $endsAt    = (clone $startsAt)->modify('+2 hours');
            $createdAt = (clone $startsAt)->modify('-14 days');

            $title = 'Laravel CI ' . ucfirst($this->faker->words(random_int(2, 4), true));

            $eventRows[] = [
                'created_by'          => $userIds[array_rand($userIds)],
                'title'               => mb_substr($title, 0, 190),
                'slug'                => Str::slug(mb_substr($title, 0, 200)) . '-lt' . ($i + 1),
                'description'         => $this->faker->paragraphs(3, true),
                'type'                => $eventTypes[array_rand($eventTypes)],
                'location'            => 'Abidjan, Plateau',
                'starts_at'           => $startsAt->format('Y-m-d H:i:s'),
                'ends_at'             => $endsAt->format('Y-m-d H:i:s'),
                'status'              => $isPast ? 'completed' : 'published',
                'capacity'            => random_int(50, 300),
                'registrations_count' => 0,
                'waitlist_enabled'    => false,
                'is_paid'             => false,
                'created_at'          => $createdAt->format('Y-m-d H:i:s'),
                'updated_at'          => now()->format('Y-m-d H:i:s'),
            ];
        }

        foreach ($eventRows as $row) {
            DB::table('events')->insert($row);
        }

        $eventIds    = DB::table('events')->where('id', '>', $maxEvtId)->pluck('id')->toArray();
        $regStatuses = ['pending', 'confirmed', 'confirmed', 'confirmed', 'attended', 'cancelled'];
        $regRows     = [];
        $regCountMap = [];

        foreach ($eventIds as $eId) {
            $nb = random_int(20, 180);
            $shuffledUsers = $userIds;
            shuffle($shuffledUsers);
            $participants = array_slice($shuffledUsers, 0, $nb);
            $regCountMap[$eId] = count($participants);

            foreach ($participants as $uid) {
                $regRows[] = [
                    'event_id'       => $eId,
                    'user_id'        => $uid,
                    'status'         => $regStatuses[array_rand($regStatuses)],
                    'payment_status' => 'free',
                    'ticket_number'  => strtoupper(Str::random(10)),
                    'ticket_qr_token'=> Str::random(64),
                    'registered_at'  => now()->subDays(random_int(1, 100))->format('Y-m-d H:i:s'),
                    'created_at'     => now()->subDays(random_int(1, 100))->format('Y-m-d H:i:s'),
                    'updated_at'     => now()->format('Y-m-d H:i:s'),
                ];
            }
        }

        foreach (array_chunk($regRows, self::BATCH) as $chunk) {
            DB::table('event_registrations')->insertOrIgnore($chunk);
        }

        foreach ($regCountMap as $eId => $count) {
            DB::table('events')->where('id', $eId)->update(['registrations_count' => $count]);
        }

        $totalRegs = array_sum($regCountMap);
        $this->command->info("     " . count($eventIds) . " événements, {$totalRegs} inscriptions.");

        // ── 5. Entreprises + offres d'emploi ───────────────────────────────────
        $this->command->info('  [5/9] Création de ' . number_format(self::COMPANIES) . ' entreprises et ' . number_format(self::JOB_OFFERS) . ' offres...');

        $maxCompId  = (int) DB::table('companies')->max('id');
        $companyRows = [];

        for ($i = 0; $i < self::COMPANIES; $i++) {
            $name = $this->faker->company() . ' LT' . ($i + 1);
            $companyRows[] = [
                'name'        => $name,
                'slug'        => Str::slug($name),
                'description' => $this->faker->catchPhrase(),
                'country'     => 'Côte d\'Ivoire',
                'city'        => 'Abidjan',
                'is_verified' => true,
                'created_at'  => now()->subDays(random_int(30, 400))->format('Y-m-d H:i:s'),
                'updated_at'  => now()->format('Y-m-d H:i:s'),
            ];
        }

        foreach ($companyRows as $row) {
            DB::table('companies')->insert($row);
        }

        $companyIds    = DB::table('companies')->where('id', '>', $maxCompId)->pluck('id')->toArray();
        $contractTypes = ['cdi', 'cdd', 'freelance', 'internship'];
        $jobLevels     = ['junior', 'intermediate', 'senior', 'lead'];
        $maxJobId      = (int) DB::table('job_offers')->max('id');
        $jobRows       = [];

        for ($i = 0; $i < self::JOB_OFFERS; $i++) {
            $title       = mb_substr($this->faker->jobTitle(), 0, 100) . ' LT' . ($i + 1);
            $publishedAt = now()->subDays(random_int(1, 90))->format('Y-m-d H:i:s');
            $expiresAt   = now()->addDays(random_int(15, 60))->format('Y-m-d H:i:s');

            $jobRows[] = [
                'company_id'    => $companyIds[array_rand($companyIds)],
                'posted_by'     => $userIds[array_rand($userIds)],
                'title'         => $title,
                'slug'          => Str::slug($title),
                'description'   => $this->faker->paragraphs(3, true),
                'contract_type' => $contractTypes[array_rand($contractTypes)],
                'level'         => $jobLevels[array_rand($jobLevels)],
                'location'      => 'Abidjan',
                'country'       => 'Côte d\'Ivoire',
                'status'        => 'active',
                'published_at'  => $publishedAt,
                'expires_at'    => $expiresAt,
                'created_at'    => $publishedAt,
                'updated_at'    => $publishedAt,
            ];
        }

        foreach (array_chunk($jobRows, self::BATCH) as $chunk) {
            DB::table('job_offers')->insert($chunk);
        }

        $jobIds     = DB::table('job_offers')->where('id', '>', $maxJobId)->pluck('id')->toArray();
        $appStatuses = ['pending', 'pending', 'viewed', 'shortlisted', 'accepted', 'rejected'];
        $appRows    = [];
        $favRows    = [];

        foreach ($jobIds as $jId) {
            // Candidatures
            $nb = random_int(3, 40);
            $shuffledUsers = $userIds;
            shuffle($shuffledUsers);
            foreach (array_slice($shuffledUsers, 0, $nb) as $uid) {
                $appRows[] = [
                    'job_offer_id' => $jId,
                    'user_id'      => $uid,
                    'cover_letter' => $this->faker->paragraph(),
                    'status'       => $appStatuses[array_rand($appStatuses)],
                    'created_at'   => now()->subDays(random_int(1, 60))->format('Y-m-d H:i:s'),
                    'updated_at'   => now()->format('Y-m-d H:i:s'),
                ];
            }

            // Favoris
            $nbFav = random_int(0, 25);
            shuffle($shuffledUsers);
            foreach (array_slice($shuffledUsers, 0, $nbFav) as $uid) {
                $favRows[] = [
                    'job_offer_id' => $jId,
                    'user_id'      => $uid,
                    'created_at'   => now()->subDays(random_int(1, 60))->format('Y-m-d H:i:s'),
                    'updated_at'   => now()->format('Y-m-d H:i:s'),
                ];
            }
        }

        foreach (array_chunk($appRows, self::BATCH) as $chunk) {
            DB::table('job_applications')->insertOrIgnore($chunk);
        }
        foreach (array_chunk($favRows, 1000) as $chunk) {
            DB::table('job_favorites')->insertOrIgnore($chunk);
        }

        $this->command->info('     ' . count($jobIds) . ' offres, ' . count($appRows) . ' candidatures, ' . count($favRows) . ' favoris.');

        // ── 6. Newsletter ─────────────────────────────────────────────────────
        $this->command->info('  [6/9] Création de ' . number_format(self::NEWSLETTER) . ' abonnés newsletter...');

        $maxNlId  = (int) DB::table('newsletter_subscribers')->max('id');
        $nlRows   = [];

        for ($i = 0; $i < self::NEWSLETTER; $i++) {
            $nlRows[] = [
                'email'           => 'lt' . $i . '_' . $this->faker->unique()->safeEmail(),
                'name'            => $this->faker->firstName(),
                'token'           => Str::random(64),
                'unsubscribed_at' => random_int(0, 4) === 0 ? now()->subDays(random_int(1, 200))->format('Y-m-d H:i:s') : null,
                'created_at'      => now()->subDays(random_int(1, 500))->format('Y-m-d H:i:s'),
                'updated_at'      => now()->format('Y-m-d H:i:s'),
            ];
        }

        foreach (array_chunk($nlRows, self::BATCH) as $chunk) {
            DB::table('newsletter_subscribers')->insertOrIgnore($chunk);
        }

        $this->command->info('     ' . DB::table('newsletter_subscribers')->where('id', '>', $maxNlId)->count() . ' abonnés créés.');

        // ── 7. Cartes membres ─────────────────────────────────────────────────
        $this->command->info('  [7/9] Création des cartes membres...');

        $cardUsers = array_slice($userIds, 0, (int) (count($userIds) * 0.65));
        $cardRows  = [];

        foreach ($cardUsers as $uid) {
            $activatedAt = now()->subDays(random_int(1, 300))->format('Y-m-d H:i:s');
            $cardRows[]  = [
                'user_id'        => $uid,
                'level'          => random_int(1, 5),
                'is_active'      => true,
                'forced_by_admin' => false,
                'activated_at'   => $activatedAt,
                'created_at'     => $activatedAt,
                'updated_at'     => $activatedAt,
            ];
        }

        foreach (array_chunk($cardRows, self::BATCH) as $chunk) {
            DB::table('member_cards')->insertOrIgnore($chunk);
        }

        $this->command->info('     ' . count($cardRows) . ' cartes créées.');

        // ── 8. Analytics simulées ─────────────────────────────────────────────
        $this->command->info('  [8/9] Génération des vues de pages analytics...');

        $pagePaths   = [
            '/', '/forum', '/blog', '/events', '/jobs',
            '/forum/questions', '/blog/articles', '/about',
        ];
        $deviceTypes = ['desktop', 'desktop', 'desktop', 'mobile', 'tablet'];
        $pvRows = [];

        for ($i = 0; $i < 20_000; $i++) {
            $uid      = random_int(0, 9) > 2 ? $userIds[array_rand($userIds)] : null;
            $pvRows[] = [
                'session_id'  => 'lt_' . Str::random(60),
                'user_id'     => $uid,
                'path'        => $pagePaths[array_rand($pagePaths)],
                'device_type' => $deviceTypes[array_rand($deviceTypes)],
                'browser'     => 'LoadTest',
                'method'      => 'GET',
                'status_code' => 200,
                'duration_ms' => random_int(50, 800),
                'created_at'  => now()->subDays(random_int(0, 90))->format('Y-m-d H:i:s'),
            ];
        }

        foreach (array_chunk($pvRows, 1000) as $chunk) {
            DB::table('analytics_page_views')->insertOrIgnore($chunk);
        }

        $this->command->info('     20 000 vues de pages insérées.');

        // ── 9. Résumé ─────────────────────────────────────────────────────────
        $this->command->info('  [9/9] Terminé !');
        $this->command->info('');
        $this->command->table(
            ['Entité', 'Lignes insérées'],
            [
                ['users',                number_format(count($userIds))],
                ['profiles',             number_format(count($userIds))],
                ['questions',            number_format(count($questionIds))],
                ['answers',              number_format(count($answerRows))],
                ['votes (questions)',     number_format(count($voteRows))],
                ['comments (total)',      number_format(count($commentRows))],
                ['articles',             number_format(count($articleIds))],
                ['events',               number_format(count($eventIds))],
                ['event_registrations',  number_format($totalRegs)],
                ['companies',            number_format(count($companyIds))],
                ['job_offers',           number_format(count($jobIds))],
                ['job_applications',     number_format(count($appRows))],
                ['job_favorites',        number_format(count($favRows))],
                ['newsletter_subscribers', number_format(count($nlRows))],
                ['member_cards',         number_format(count($cardRows))],
                ['analytics_page_views', '20 000'],
            ]
        );
        $this->command->info('');
        $this->command->info('  Pour supprimer ces données : php artisan db:seed --class=LoadTestResetSeeder');
        $this->command->info('');
    }
}
