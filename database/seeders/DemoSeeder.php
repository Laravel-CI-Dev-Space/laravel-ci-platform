<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ArticleLevel;
use App\Enums\ArticleStatus;
use App\Enums\EventStatus;
use App\Enums\JobContractType;
use App\Enums\JobLevel;
use App\Enums\JobOfferStatus;
use App\Enums\UserRole;
use App\Models\Answer;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Company;
use App\Models\CompanyAccount;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\JobOffer;
use App\Models\NewsletterSubscriber;
use App\Models\Profile;
use App\Models\Question;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    private string $mascot;

    public function run(): void
    {
        $this->mascot = asset('assets/web/img/mascot.png');

        $this->command->info('  Seeding members...');
        $members = $this->seedMembers();

        $this->command->info('  Seeding articles...');
        $this->seedArticles($members);

        $this->command->info('  Seeding forum questions...');
        $this->seedQuestions($members);

        $this->command->info('  Seeding events registrations...');
        $this->seedEventRegistrations($members);

        $this->command->info('  Seeding companies & job offers...');
        $this->seedCompanies($members);

        $this->command->info('  Seeding newsletter subscribers...');
        $this->seedNewsletterSubscribers($members);
    }

    // ──────────────────────────────────────────────────────────────
    // MEMBRES
    // ──────────────────────────────────────────────────────────────

    private function seedMembers(): array
    {
        $data = [
            ['name' => 'Kouamé Assi',      'email' => 'kouame.assi@laravelci.demo',    'username' => 'kouame-assi',    'city' => 'Abidjan',     'bio' => 'Développeur Laravel passionné par les APIs RESTful.',        'stack' => ['Laravel', 'PHP', 'MySQL', 'Vue.js']],
            ['name' => 'Fatou Traoré',     'email' => 'fatou.traore@laravelci.demo',   'username' => 'fatou-traore',   'city' => 'Bouaké',      'bio' => 'Full-stack developer, Laravel & React.',                     'stack' => ['Laravel', 'React', 'PostgreSQL', 'Docker']],
            ['name' => 'Ismaël Coulibaly', 'email' => 'ismael.coulibaly@laravelci.demo','username' => 'ismael-coul',   'city' => 'Abidjan',     'bio' => 'Backend engineer spécialisé microservices.',                 'stack' => ['Laravel', 'PHP', 'Redis', 'RabbitMQ']],
            ['name' => 'Adjoua Koffi',     'email' => 'adjoua.koffi@laravelci.demo',   'username' => 'adjoua-koffi',  'city' => 'San-Pédro',   'bio' => 'Développeuse web freelance.',                                'stack' => ['Laravel', 'Livewire', 'Alpine.js', 'MySQL']],
            ['name' => 'Mamadou Bah',      'email' => 'mamadou.bah@laravelci.demo',    'username' => 'mamadou-bah',   'city' => 'Abidjan',     'bio' => 'CTO startup fintech ivoirienne.',                            'stack' => ['Laravel', 'Vue.js', 'AWS', 'Kubernetes']],
            ['name' => 'Aya Ouédraogo',    'email' => 'aya.ouedraogo@laravelci.demo',  'username' => 'aya-ouedraogo', 'city' => 'Abidjan',     'bio' => 'Ingénieure logiciel, contributrice open-source.',            'stack' => ['Laravel', 'PHP', 'TDD', 'CI/CD']],
            ['name' => 'Konan Gbagbo',     'email' => 'konan.gbagbo@laravelci.demo',   'username' => 'konan-gbagbo',  'city' => 'Yamoussoukro','bio' => 'Enseignant informatique & développeur Laravel.',             'stack' => ['Laravel', 'PHP', 'MySQL', 'Bootstrap']],
            ['name' => 'Sandrine Aboa',    'email' => 'sandrine.aboa@laravelci.demo',  'username' => 'sandrine-aboa', 'city' => 'Abidjan',     'bio' => 'Développeuse junior en quête de ses premiers clients.',      'stack' => ['Laravel', 'HTML', 'CSS', 'JavaScript']],
            ['name' => 'Thierry Kouassi',  'email' => 'thierry.kouassi@laravelci.demo','username' => 'thierry-k',     'city' => 'Abidjan',     'bio' => 'DevOps & Laravel developer.',                                'stack' => ['Laravel', 'Docker', 'Linux', 'Nginx']],
            ['name' => 'Mariam Diallo',    'email' => 'mariam.diallo@laravelci.demo',  'username' => 'mariam-diallo', 'city' => 'Abidjan',     'bio' => 'Product manager & développeuse no-code/low-code.',           'stack' => ['Laravel', 'PHP', 'Filament', 'Livewire']],
            ['name' => 'Brice N\'Goran',   'email' => 'brice.ngoran@laravelci.demo',   'username' => 'brice-ngoran',  'city' => 'Daloa',       'bio' => 'Passionné de sécurité applicative sous Laravel.',            'stack' => ['Laravel', 'PHP', 'Security', 'OAuth']],
            ['name' => 'Clarisse Yao',     'email' => 'clarisse.yao@laravelci.demo',   'username' => 'clarisse-yao',  'city' => 'Abidjan',     'bio' => 'UX/UI designer qui code aussi en Laravel.',                  'stack' => ['Laravel', 'Tailwind', 'Figma', 'Alpine.js']],
        ];

        $members = [];
        $githubOffset = 50000000;

        foreach ($data as $i => $m) {
            $user = User::updateOrCreate(
                ['email' => $m['email']],
                [
                    'name'              => $m['name'],
                    'github_id'         => (string) ($githubOffset + $i),
                    'github_username'   => $m['username'],
                    'avatar'            => $this->mascot,
                    'is_active'         => true,
                    'email_verified_at' => now()->subDays(rand(10, 365)),
                    'last_login_at'     => now()->subDays(rand(0, 30)),
                ]
            );
            $user->syncRoles([UserRole::Member->value]);

            Profile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'country'          => "Côte d'Ivoire",
                    'city'             => $m['city'],
                    'bio'              => $m['bio'],
                    'laravel_level'    => collect(['debutant', 'intermediaire', 'avance', 'expert'])->random(),
                    'years_experience' => collect(['moins_1_an', '1_3_ans', '3_5_ans', '5_10_ans'])->random(),
                    'tech_stack'       => $m['stack'],
                    'academic_level'   => collect(['licence', 'master_ingenieur', 'bts'])->random(),
                    'job_status'       => collect(['en_fonction', 'freelance', 'recherche_emploi', 'entrepreneur'])->random(),
                ]
            );

            $members[] = $user;
        }

        return $members;
    }

    // ──────────────────────────────────────────────────────────────
    // ARTICLES
    // ──────────────────────────────────────────────────────────────

    private function seedArticles(array $members): void
    {
        $tags = Tag::whereIn('slug', ['laravel', 'php', 'livewire', 'filament', 'mysql', 'api', 'security', 'deployment'])->get()->keyBy('slug');

        $articles = [
            [
                'title'   => "Construire une API REST robuste avec Laravel 13",
                'excerpt' => "Découvrez les meilleures pratiques pour concevoir et sécuriser une API REST avec Laravel 13, de l'authentification aux tests.",
                'body'    => "<h2>Introduction</h2><p>Laravel 13 apporte des améliorations majeures pour le développement d'APIs REST. Dans cet article, nous allons construire une API complète en suivant les meilleures pratiques.</p><h2>Structure du projet</h2><p>La première étape est de bien structurer votre projet. Utilisez les Resource Controllers et les Form Requests pour garder votre code propre et testable.</p><h2>Authentification avec Sanctum</h2><p>Laravel Sanctum est la solution recommandée pour les APIs mobiles et SPAs. Il offre une authentification légère basée sur les tokens.</p><pre><code>php artisan install:api</code></pre><h2>Tests avec Pest</h2><p>Pest est le framework de test recommandé pour Laravel. Sa syntaxe expressive rend les tests plus agréables à écrire.</p><h2>Conclusion</h2><p>Avec ces fondations, votre API Laravel est prête pour la production.</p>",
                'level'   => ArticleLevel::Intermediate,
                'tags'    => ['laravel', 'api'],
                'days'    => 30,
            ],
            [
                'title'   => "Filament 5 — Guide complet pour les débutants",
                'excerpt' => "Filament 5 révolutionne la création de panneaux d'administration. Voici comment démarrer rapidement.",
                'body'    => "<h2>Qu'est-ce que Filament ?</h2><p>Filament est un framework d'administration full-stack pour Laravel. La version 5 apporte des performances améliorées et une nouvelle architecture basée sur les Schemas.</p><h2>Installation</h2><pre><code>composer require filament/filament\nphp artisan filament:install --panels</code></pre><h2>Créer votre premier Resource</h2><p>Un Resource Filament génère automatiquement les pages de liste, création et édition pour votre modèle Eloquent.</p><h2>Personnalisation</h2><p>Filament offre une personnalisation poussée via les Schemas, Tables et Forms. Vous pouvez créer des champs personnalisés et des actions complexes.</p>",
                'level'   => ArticleLevel::Beginner,
                'tags'    => ['laravel', 'filament'],
                'days'    => 20,
            ],
            [
                'title'   => "Livewire 4 et les Composants temps réel",
                'excerpt' => "Livewire 4 simplifie le développement d'interfaces dynamiques sans JavaScript. Un tour d'horizon des nouvelles fonctionnalités.",
                'body'    => "<h2>Livewire 4 — Quoi de neuf ?</h2><p>Livewire 4 introduit une nouvelle architecture avec les Volt functional components et une meilleure intégration avec Alpine.js.</p><h2>Volt — Composants fonctionnels</h2><p>Volt permet d'écrire des composants Livewire en utilisant une syntaxe fonctionnelle, similaire à Vue 3 Composition API.</p><pre><code>&lt;?php\nuse function Livewire\\Volt\\{state, computed};\n\nstate(['count' => 0]);\n\n\$increment = fn() => \$this->count++;\n?&gt;</code></pre><h2>Performance</h2><p>Livewire 4 est significativement plus rapide grâce à une nouvelle implémentation du système de morphing DOM.</p>",
                'level'   => ArticleLevel::Intermediate,
                'tags'    => ['laravel', 'livewire'],
                'days'    => 15,
            ],
            [
                'title'   => "Sécuriser son application Laravel en production",
                'excerpt' => "Les 10 points essentiels à vérifier avant de déployer votre application Laravel en production.",
                'body'    => "<h2>Checklist sécurité Laravel</h2><p>La sécurité est souvent négligée lors du développement. Voici les points essentiels à vérifier.</p><h2>1. Variables d'environnement</h2><p>Ne commitez jamais votre fichier .env. Utilisez des variables d'environnement système en production.</p><h2>2. CSRF Protection</h2><p>Laravel inclut une protection CSRF par défaut. Assurez-vous de ne pas l'avoir désactivée.</p><h2>3. SQL Injection</h2><p>Utilisez toujours l'ORM Eloquent ou les requêtes préparées. Évitez les interpolations de chaînes dans les requêtes SQL.</p><h2>4. Headers de sécurité</h2><p>Configurez les headers HTTP de sécurité comme Content-Security-Policy, X-Frame-Options et HSTS.</p><h2>5. Rate Limiting</h2><p>Laravel offre un système de rate limiting intégré. Utilisez-le pour protéger vos routes d'authentification.</p>",
                'level'   => ArticleLevel::Advanced,
                'tags'    => ['laravel', 'security'],
                'days'    => 10,
            ],
            [
                'title'   => "Déployer Laravel sur un hébergement mutualisé en 2026",
                'excerpt' => "Guide pratique pour déployer une application Laravel sur un hébergement mutualisé avec les contraintes qui en découlent.",
                'body'    => "<h2>Les défis du mutualisé</h2><p>Déployer Laravel sur un hébergement mutualisé présente des défis spécifiques : pas de queue workers, pas de WebSockets, pas d'accès root.</p><h2>Configuration du .htaccess</h2><p>Sur Apache, vous devez configurer votre .htaccess pour pointer vers le dossier public de Laravel.</p><pre><code>RewriteEngine On\nRewriteRule ^(.*)$ public/$1 [L]</code></pre><h2>Optimisations</h2><p>Sur un hébergement mutualisé, chaque milliseconde compte. Utilisez la mise en cache de configuration, routes et vues.</p><pre><code>php artisan config:cache\nphp artisan route:cache\nphp artisan view:cache</code></pre>",
                'level'   => ArticleLevel::Intermediate,
                'tags'    => ['laravel', 'deployment'],
                'days'    => 5,
            ],
            [
                'title'   => "MySQL vs PostgreSQL pour vos projets Laravel",
                'excerpt' => "Comparaison objective des deux SGBD les plus populaires avec Laravel. Quel choix pour quel contexte ?",
                'body'    => "<h2>Introduction</h2><p>Laravel supporte parfaitement MySQL et PostgreSQL. Mais lequel choisir pour votre prochain projet ?</p><h2>MySQL / MariaDB</h2><p>MySQL reste le choix par défaut de la communauté Laravel. Sa simplicité d'administration et sa large adoption en hébergement mutualisé en font un excellent choix pour démarrer.</p><h2>PostgreSQL</h2><p>PostgreSQL offre des fonctionnalités avancées : types JSON natifs, recherche full-text, fenêtres analytiques. Il est idéal pour les applications complexes.</p><h2>Laravel Eloquent et les deux</h2><p>La bonne nouvelle : Eloquent fonctionne de manière identique sur les deux SGBD. La migration d'un projet est généralement straightforward.</p>",
                'level'   => ArticleLevel::Beginner,
                'tags'    => ['php', 'mysql'],
                'days'    => 3,
            ],
        ];

        $commentTexts = [
            "Excellent article, très bien expliqué !",
            "Merci pour ce partage, j'ai appris beaucoup de choses.",
            "Tu aurais un dépôt GitHub avec le code complet ?",
            "J'ai suivi ton tutoriel et ça fonctionne parfaitement.",
            "Super article ! Une question : comment gérer les erreurs de validation côté client ?",
            "Très utile pour mon projet actuel. Merci !",
            "Est-ce que cette approche fonctionne aussi avec PostgreSQL ?",
            "J'aurais aimé plus de détails sur la partie tests.",
            "Bravo, c'est exactement ce que je cherchais.",
        ];

        foreach ($articles as $i => $data) {
            $author = $members[$i % count($members)];
            $slug   = Str::slug($data['title']);

            $article = Article::updateOrCreate(
                ['slug' => $slug],
                [
                    'user_id'      => $author->id,
                    'reviewed_by'  => $author->id,
                    'title'        => $data['title'],
                    'slug'         => $slug,
                    'excerpt'      => $data['excerpt'],
                    'body'         => $data['body'],
                    'body_html'    => $data['body'],
                    'cover_image'  => null,
                    'level'        => $data['level'],
                    'status'       => ArticleStatus::Published,
                    'published_at' => now()->subDays($data['days']),
                    'reviewed_at'  => now()->subDays($data['days']),
                    'views_count'  => rand(40, 300),
                ]
            );

            // Attach tags
            $tagIds = collect($data['tags'])
                ->map(fn ($slug) => $tags->get($slug)?->id)
                ->filter()
                ->toArray();
            $article->tags()->sync($tagIds);

            // Comments on article
            $commentCount = rand(2, 4);
            for ($c = 0; $c < $commentCount; $c++) {
                $commenter = $members[($i + $c + 1) % count($members)];
                Comment::create([
                    'commentable_id'   => $article->id,
                    'commentable_type' => Article::class,
                    'user_id'          => $commenter->id,
                    'body'             => $commentTexts[($i + $c) % count($commentTexts)],
                ]);
            }

            $article->update(['comments_count' => $article->comments()->count()]);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // QUESTIONS FORUM
    // ──────────────────────────────────────────────────────────────

    private function seedQuestions(array $members): void
    {
        $tags = Tag::whereIn('slug', ['laravel', 'php', 'livewire', 'eloquent', 'mysql', 'api', 'deployment', 'security'])->get()->keyBy('slug');

        $questions = [
            [
                'title' => "Comment optimiser les requêtes N+1 avec Eloquent ?",
                'body'  => "<p>J'ai une application Laravel avec des relations Eloquent complexes et j'observe des problèmes de performance. J'ai notamment une liste d'articles avec leurs auteurs et leurs tags qui génère des dizaines de requêtes.</p><p>J'utilise déjà <code>with()</code> pour l'eager loading mais certaines relations imbriquées continuent de générer des N+1.</p><p>Quelqu'un a-t-il une stratégie complète pour détecter et corriger ces problèmes ?</p>",
                'tags'  => ['laravel', 'eloquent'],
                'answers' => [
                    "Utilise le package <code>barryvdh/laravel-debugbar</code> pour visualiser toutes les requêtes en développement. Pour les N+1, <code>with()</code> est la solution mais pense aussi à <code>withCount()</code> pour les comptages. Tu peux aussi activer <code>Model::preventLazyLoading()</code> en développement pour détecter les chargements paresseux accidentels.",
                    "En complément, pense à <code>select()</code> pour ne récupérer que les colonnes dont tu as besoin. Sur les listes, tu n'as souvent pas besoin du corps complet des articles, juste le titre et l'excerpt.",
                ],
            ],
            [
                'title' => "Livewire et la gestion des fichiers volumineux",
                'body'  => "<p>Je développe une fonctionnalité d'upload de fichiers avec Livewire 4 et je rencontre des problèmes avec les fichiers supérieurs à 10MB.</p><p>L'upload timeout avant d'être terminé. J'ai essayé d'augmenter <code>upload_max_filesize</code> dans php.ini mais le problème persiste.</p><p>Comment gérez-vous les uploads volumineux avec Livewire ?</p>",
                'tags'  => ['laravel', 'livewire'],
                'answers' => [
                    "Le problème vient probablement du timeout PHP ou de la configuration de Nginx. Pour Livewire, tu dois aussi ajuster <code>livewire.temporary_file_upload.rules</code> dans le fichier de config. Ajoute aussi <code>client_max_body_size</code> dans ta config Nginx.",
                    "Pour les fichiers très volumineux, je recommande de passer par une pré-signature S3 ou une upload directe vers le CDN depuis le front. Livewire n'est pas vraiment fait pour des uploads > 50MB.",
                ],
            ],
            [
                'title' => "Filament — Comment créer un champ personnalisé ?",
                'body'  => "<p>J'ai besoin d'un champ de formulaire Filament qui affiche une carte interactive pour sélectionner une adresse géographique.</p><p>J'ai regardé la documentation mais je ne trouve pas d'exemple complet de création d'un champ personnalisé avec des interactions JavaScript.</p><p>Quelqu'un a déjà créé un custom field Filament avec Alpine.js ?</p>",
                'tags'  => ['laravel', 'php'],
                'answers' => [
                    "Oui, j'ai fait ça pour un projet. Tu dois créer une classe qui étend <code>Field</code>, définir une vue Blade avec Alpine.js, et implémenter <code>getView()</code>. L'intégration avec Livewire se fait via les attributs <code>wire:model</code>.",
                ],
            ],
            [
                'title' => "Comment gérer les migrations en production sans downtime ?",
                'body'  => "<p>Mon application commence à avoir du trafic et je ne peux plus me permettre d'exécuter des migrations qui lockent les tables.</p><p>J'utilise MySQL 8. Comment éviter les locks lors des migrations qui ajoutent des colonnes à des tables volumineuses (>1M lignes) ?</p>",
                'tags'  => ['mysql', 'deployment'],
                'answers' => [
                    "Utilise <code>ALGORITHM=INSTANT</code> pour MySQL 8 — beaucoup d'opérations (ajout de colonne, valeur par défaut) sont instantanées. Pour les opérations non-INSTANT, utilise gh-ost ou pt-online-schema-change qui copient la table en arrière-plan.",
                    "Une autre stratégie : utilise des migrations en plusieurs étapes. D'abord ajoute la colonne nullable (INSTANT), puis backfill les données, puis ajoute la contrainte NOT NULL si nécessaire.",
                ],
            ],
            [
                'title' => "Implémenter une recherche full-text avec Laravel Scout",
                'body'  => "<p>Je veux ajouter une recherche dans mon application Laravel. J'ai regardé Scout avec Meilisearch mais l'hébergement pose problème (on est sur un mutualisé).</p><p>Quelles alternatives à Meilisearch fonctionnent bien sur un mutualisé ? J'ai vu <code>SCOUT_DRIVER=database</code> et <code>collection</code>.</p>",
                'tags'  => ['laravel', 'mysql'],
                'answers' => [
                    "Le driver <code>database</code> de Scout utilise les full-text indexes MySQL. Il est suffisant pour la plupart des cas sur mutualisé. Configure <code>SCOUT_DRIVER=database</code> et lance <code>php artisan scout:import</code>. Les performances sont correctes jusqu'à ~100k entrées.",
                    "J'utilise le driver <code>collection</code> pour les petits datasets (<10k). C'est plus simple, pas de configuration, ça filtre en mémoire. Après 10k enregistrements, passe au driver database avec des index FULLTEXT MySQL.",
                ],
            ],
            [
                'title' => "Quelle architecture pour une application multi-tenants Laravel ?",
                'body'  => "<p>Je dois construire une application SaaS multi-tenants. Chaque client doit avoir ses propres données isolées.</p><p>J'hésite entre plusieurs approches :</p><ul><li>Une base de données par tenant</li><li>Un schéma par tenant (PostgreSQL)</li><li>Une seule DB avec colonne tenant_id</li></ul><p>Quel retour d'expérience avez-vous ?</p>",
                'tags'  => ['laravel', 'api'],
                'answers' => [
                    "Pour la majorité des projets, la colonne <code>tenant_id</code> avec un Global Scope Eloquent est la solution la plus simple à maintenir. Regarde le package <code>spatie/laravel-multitenancy</code> qui gère ça proprement.",
                    "Si ton SaaS a des clients avec des volumes de données très différents ou des exigences de compliance strictes, la DB-per-tenant vaut l'investissement. Sinon, tenant_id + Global Scope est largement suffisant et beaucoup plus simple à déployer.",
                ],
            ],
        ];

        foreach ($questions as $i => $data) {
            $asker = $members[$i % count($members)];
            $slug  = Str::slug($data['title']);

            $question = Question::updateOrCreate(
                ['slug' => $slug],
                [
                    'user_id'           => $asker->id,
                    'title'             => $data['title'],
                    'slug'              => $slug,
                    'body'              => $data['body'],
                    'status'            => 'published',
                    'views_count'       => rand(50, 500),
                    'votes_score'       => rand(1, 20),
                    'last_activity_at'  => now()->subDays(rand(1, 60)),
                ]
            );

            // Tags
            $tagIds = collect($data['tags'])
                ->map(fn ($slug) => $tags->get($slug)?->id)
                ->filter()
                ->toArray();
            $question->tags()->sync($tagIds);

            // Answers
            $acceptedAnswerId = null;
            foreach ($data['answers'] as $j => $answerBody) {
                $responder = $members[($i + $j + 2) % count($members)];
                $answer = Answer::create([
                    'question_id' => $question->id,
                    'user_id'     => $responder->id,
                    'body'        => "<p>{$answerBody}</p>",
                    'body_html'   => "<p>{$answerBody}</p>",
                    'votes_score' => rand(1, 15),
                    'is_accepted' => $j === 0,
                ]);
                if ($j === 0) {
                    $acceptedAnswerId = $answer->id;
                }

                // Comment on answer
                if (rand(0, 1)) {
                    $commenter = $members[($i + $j + 3) % count($members)];
                    Comment::create([
                        'commentable_id'   => $answer->id,
                        'commentable_type' => Answer::class,
                        'user_id'          => $commenter->id,
                        'body'             => "Merci pour cette réponse très complète !",
                    ]);
                }
            }

            $question->update([
                'accepted_answer_id' => $acceptedAnswerId,
                'answers_count'      => $question->answers()->count(),
            ]);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // INSCRIPTIONS ÉVÉNEMENTS
    // ──────────────────────────────────────────────────────────────

    private function seedEventRegistrations(array $members): void
    {
        $events = Event::where('status', EventStatus::Published)->get();

        if ($events->isEmpty()) {
            return;
        }

        foreach ($events as $event) {
            $attendees = collect($members)->random(min(rand(5, 10), count($members)));

            foreach ($attendees as $member) {
                EventRegistration::firstOrCreate(
                    ['event_id' => $event->id, 'user_id' => $member->id],
                    [
                        'status'        => 'confirmed',
                        'registered_at' => now()->subDays(rand(1, 20)),
                        'ical_token'    => Str::random(32),
                    ]
                );
            }

            $event->update(['registrations_count' => $event->registrations()->count()]);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // ENTREPRISES & OFFRES D'EMPLOI
    // ──────────────────────────────────────────────────────────────

    private function seedCompanies(array $members): void
    {
        // ── Entreprise 1 : TechAbidjan ────────────────────────────
        $company1 = Company::updateOrCreate(
            ['slug' => 'tech-abidjan'],
            [
                'submitted_by' => $members[0]->id,
                'name'         => 'TechAbidjan SARL',
                'slug'         => 'tech-abidjan',
                'description'  => 'Agence de développement web et mobile spécialisée Laravel & React Native. Nous accompagnons les startups et PME ivoiriennes dans leur transformation digitale.',
                'logo'         => null,
                'website'      => 'https://techabidjan.ci',
                'email'        => 'contact@techabidjan.ci',
                'phone'        => '+225 07 00 11 22 33',
                'country'      => "Côte d'Ivoire",
                'city'         => 'Abidjan',
                'is_verified'  => true,
            ]
        );

        $account1 = CompanyAccount::updateOrCreate(
            ['email' => 'rh@techabidjan.ci'],
            [
                'company_id'   => $company1->id,
                'first_name'   => 'Adjoua',
                'last_name'    => 'Mensah',
                'email'        => 'rh@techabidjan.ci',
                'password'     => Hash::make('password'),
                'position'     => 'Responsable RH',
                'phone'        => '+225 07 00 11 22 34',
                'status'       => 'active',
            ]
        );

        // Offres TechAbidjan
        $this->createJobOffer($company1, $account1, [
            'title'         => "Développeur Laravel Senior",
            'slug'          => 'dev-laravel-senior-techabidjan',
            'description'   => "<h2>À propos du poste</h2><p>TechAbidjan recrute un développeur Laravel Senior pour rejoindre notre équipe tech en pleine croissance.</p><h2>Missions</h2><ul><li>Conception et développement d'applications Laravel</li><li>Code review et mentoring des juniors</li><li>Architecture des solutions techniques</li><li>Participation aux décisions produit</li></ul><h2>Profil recherché</h2><ul><li>5+ ans d'expérience PHP/Laravel</li><li>Maîtrise de Livewire et des APIs RESTful</li><li>Expérience en TDD</li><li>Bon communicant</li></ul>",
            'contract_type' => JobContractType::Cdi,
            'level'         => JobLevel::Senior,
            'location'      => 'Abidjan, Cocody',
            'is_remote'     => true,
            'is_hybrid'     => false,
            'salary_min'    => 800000,
            'salary_max'    => 1200000,
            'currency'      => 'XOF',
            'salary_visible'=> true,
        ]);

        $this->createJobOffer($company1, $account1, [
            'title'         => "Développeur Frontend Vue.js / Tailwind",
            'slug'          => 'dev-frontend-vuejs-techabidjan',
            'description'   => "<h2>À propos du poste</h2><p>Nous recherchons un développeur frontend passionné par les interfaces modernes et performantes.</p><h2>Missions</h2><ul><li>Développement d'interfaces Vue.js 3</li><li>Intégration avec des APIs Laravel</li><li>Optimisation des performances frontend</li></ul><h2>Profil</h2><ul><li>2+ ans Vue.js</li><li>Bonne maîtrise de Tailwind CSS</li><li>Connaissance de TypeScript appréciée</li></ul>",
            'contract_type' => JobContractType::Cdi,
            'level'         => JobLevel::Intermediate,
            'location'      => 'Abidjan, Plateau',
            'is_remote'     => false,
            'is_hybrid'     => true,
            'salary_min'    => 500000,
            'salary_max'    => 750000,
            'currency'      => 'XOF',
            'salary_visible'=> true,
        ]);

        // ── Entreprise 2 : InnoveCI ───────────────────────────────
        $company2 = Company::updateOrCreate(
            ['slug' => 'innove-ci'],
            [
                'submitted_by' => $members[1]->id,
                'name'         => 'InnoveCI',
                'slug'         => 'innove-ci',
                'description'  => 'Startup fintech ivoirienne développant des solutions de paiement mobile et de gestion financière pour les PME d\'Afrique de l\'Ouest.',
                'logo'         => null,
                'website'      => 'https://innoveci.com',
                'email'        => 'jobs@innoveci.com',
                'phone'        => '+225 05 00 44 55 66',
                'country'      => "Côte d'Ivoire",
                'city'         => 'Abidjan',
                'is_verified'  => true,
            ]
        );

        $account2 = CompanyAccount::updateOrCreate(
            ['email' => 'jobs@innoveci.com'],
            [
                'company_id'   => $company2->id,
                'first_name'   => 'Mamadou',
                'last_name'    => 'Koné',
                'email'        => 'jobs@innoveci.com',
                'password'     => Hash::make('password'),
                'position'     => 'CTO',
                'phone'        => '+225 05 00 44 55 67',
                'status'       => 'active',
            ]
        );

        // Offres InnoveCI
        $this->createJobOffer($company2, $account2, [
            'title'         => "Ingénieur Backend PHP / Laravel — Fintech",
            'slug'          => 'ingenieur-backend-php-innoveci',
            'description'   => "<h2>InnoveCI recrute</h2><p>Rejoignez l'équipe technique d'InnoveCI pour construire les solutions de paiement de demain en Afrique de l'Ouest.</p><h2>Vos missions</h2><ul><li>Développement des APIs de paiement</li><li>Intégration avec les opérateurs mobile money (MTN, Orange, Moov)</li><li>Gestion des webhooks et transactions</li><li>Sécurité et conformité PCI-DSS</li></ul><h2>Stack technique</h2><p>Laravel 13, PostgreSQL, Redis, Docker, AWS, Queues Laravel.</p>",
            'contract_type' => JobContractType::Cdi,
            'level'         => JobLevel::Senior,
            'location'      => 'Abidjan, Marcory',
            'is_remote'     => false,
            'is_hybrid'     => true,
            'salary_min'    => 900000,
            'salary_max'    => 1500000,
            'currency'      => 'XOF',
            'salary_visible'=> false,
        ]);

        $this->createJobOffer($company2, $account2, [
            'title'         => "Stage — Développeur Laravel Junior",
            'slug'          => 'stage-dev-laravel-junior-innoveci',
            'description'   => "<h2>Stage de 6 mois</h2><p>InnoveCI ouvre ses portes aux jeunes développeurs Laravel motivés pour un stage de 6 mois avec possibilité d'embauche.</p><h2>Ce que vous apprendrez</h2><ul><li>Développement en environnement production réel</li><li>Pratiques DevOps (CI/CD, Docker)</li><li>APIs de paiement africaines</li></ul><h2>Profil</h2><ul><li>Étudiant en informatique (Bac+3 minimum)</li><li>Bases en PHP et Laravel</li><li>Motivé et curieux</li></ul>",
            'contract_type' => JobContractType::Internship,
            'level'         => JobLevel::Junior,
            'location'      => 'Abidjan, Marcory',
            'is_remote'     => false,
            'is_hybrid'     => false,
            'salary_min'    => 100000,
            'salary_max'    => 150000,
            'currency'      => 'XOF',
            'salary_visible'=> true,
        ]);
    }

    private function createJobOffer(Company $company, CompanyAccount $account, array $data): void
    {
        JobOffer::updateOrCreate(
            ['slug' => $data['slug']],
            array_merge($data, [
                'company_id'    => $company->id,
                'posted_by'     => $account->id,
                'status'        => JobOfferStatus::Active,
                'country'       => "Côte d'Ivoire",
                'views_count'   => rand(30, 200),
                'published_at'  => now()->subDays(rand(1, 15)),
                'expires_at'    => now()->addDays(rand(15, 45)),
            ])
        );
    }

    // ──────────────────────────────────────────────────────────────
    // NEWSLETTER
    // ──────────────────────────────────────────────────────────────

    private function seedNewsletterSubscribers(array $members): void
    {
        // Abonne la moitié des membres automatiquement
        foreach (collect($members)->random(intval(count($members) * 0.7)) as $member) {
            NewsletterSubscriber::firstOrCreate(
                ['email' => $member->email],
                ['name' => explode(' ', $member->name)[0]]
            );
        }

        // Abonnés extérieurs
        $external = [
            ['email' => 'dev.ivoirien@gmail.demo',   'name' => 'Kouadio'],
            ['email' => 'laravel.fan.ci@yahoo.demo',  'name' => 'Aminata'],
            ['email' => 'php.abidjan@outlook.demo',   'name' => 'Serge'],
        ];

        foreach ($external as $sub) {
            NewsletterSubscriber::firstOrCreate(['email' => $sub['email']], $sub);
        }
    }
}
