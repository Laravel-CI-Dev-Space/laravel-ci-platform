<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ArticleLevel;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::whereNotNull('github_id')->first();

        if (! $author) {
            $this->command->warn('ArticleSeeder: aucun utilisateur trouvé, articles ignorés.');
            return;
        }

        $articles = [
            [
                'title'        => "La naissance de Laravel CI : l'histoire d'une communauté ivoirienne",
                'slug'         => 'naissance-laravel-ci-histoire-communaute-ivoirienne',
                'excerpt'      => "Comment deux développeurs passionnés ont transformé un groupe WhatsApp en la première communauté structurée de développeurs Laravel en Côte d'Ivoire.",
                'cover_image'  => 'mascot.png',
                'level'        => ArticleLevel::Beginner,
                'status'       => ArticleStatus::Published,
                'published_at' => now()->subMonths(2),
                'body'         => $this->articleCommunity(),
                'body_html'    => $this->articleCommunity(),
            ],
            [
                'title'        => "Laravel : naissance et évolution du framework PHP qui a tout changé",
                'slug'         => 'laravel-naissance-evolution-framework-php',
                'excerpt'      => "De la frustration d'un développeur face aux outils existants à la création d'un framework qui a révolutionné le développement PHP - l'histoire de Laravel racontée depuis Abidjan.",
                'cover_image'  => 'laravel-logo.png',
                'level'        => ArticleLevel::Beginner,
                'status'       => ArticleStatus::Published,
                'published_at' => now()->subMonth(),
                'body'         => $this->articleLaravel(),
                'body_html'    => $this->articleLaravel(),
            ],
        ];

        foreach ($articles as $data) {
            Article::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, [
                    'user_id'      => $author->id,
                    'reviewed_by'  => $author->id,
                    'views_count'  => 0,
                    'comments_count' => 0,
                ])
            );
        }
    }

    private function articleCommunity(): string
    {
        return <<<'HTML'
<p>En janvier 2026, deux développeurs abidjanais partagent un constat simple : il n'existe pas encore d'espace structuré, en français et ancré dans la réalité locale, pour les passionnés de Laravel en Côte d'Ivoire. Les ressources existent - la documentation officielle, les tutoriels YouTube, les forums anglophones - mais rien qui ressemble à une vraie communauté locale, avec des visages connus, des meetups accessibles et des échanges au rythme africain.</p>

<p>C'est de cette conviction que naît <strong>Laravel CI</strong>.</p>

<h2>Un groupe WhatsApp comme point de départ</h2>

<p>Tout commence modestement : un groupe WhatsApp nommé <em>Laravel Abidjan</em>, créé par Wilson Kouassi et Mahamadou Diaby. En quelques jours, les invitations se propagent de terminal en terminal, de bureau en bureau. Des développeurs solo qui galèrent en silence, des freelances qui jonglent entre projets clients, des étudiants en quête de mentors - tous trouvent dans ce groupe un espace où poser des questions sans craindre le jugement.</p>

<p>En deux semaines, le groupe compte plus de 80 membres. En un mois, 200. La dynamique est là.</p>

<h2>Les co-fondateurs</h2>

<p><strong>Wilson Kouassi</strong>, développeur full-stack et architecte de la plateforme, prend en charge la vision technique. Il conçoit l'infrastructure de la communauté avec une obsession pour la qualité du code et l'expérience développeur. C'est lui qui pose les fondations de ce que vous utilisez aujourd'hui.</p>

<p><strong>Mahamadou Diaby</strong>, co-fondateur et pilier de la communauté, apporte son expertise métier et sa capacité à fédérer. Il co-dirige la vision de la plateforme et s'assure que chaque nouveau membre trouve sa place rapidement.</p>

<h2>De WhatsApp à une plateforme dédiée</h2>

<p>Très vite, les limites de WhatsApp apparaissent. Les discussions disparaissent, les ressources se perdent, les nouveaux membres peinent à retrouver les échanges passés. Il faut quelque chose de plus solide.</p>

<p>La décision est prise : construire une plateforme communautaire open source, faite avec Laravel, pour les développeurs Laravel. Un forum structuré, un blog, un agenda d'événements, un espace emploi - tout ce dont une communauté tech sérieuse a besoin.</p>

<h2>Aujourd'hui : près de 1 200 membres</h2>

<p>Moins d'un an après la création du premier message WhatsApp, Laravel CI compte <strong>plus de 900 membres sur LinkedIn</strong> et <strong>340 membres actifs dans les groupes WhatsApp</strong>. Des meetups sont organisés régulièrement à Abidjan, des workshops techniques attirent des développeurs de tout niveau, et la plateforme grandit chaque semaine.</p>

<p>La communauté est fière de ses partenariats avec d'autres communautés Laravel à travers le monde : Laravel France, Laravel Cameroun, Laravel Sénégal, Laravel Denmark et Laravel Live UK London.</p>

<h2>La suite</h2>

<p>Laravel CI n'est pas qu'une plateforme - c'est un mouvement. Celui de développeurs ivoiriens qui décident de prendre leur place dans l'écosystème mondial du logiciel, avec leurs outils, leur culture et leurs ambitions. Rejoins-nous.</p>
HTML;
    }

    private function articleLaravel(): string
    {
        return <<<'HTML'
<p>En 2011, un développeur américain nommé <strong>Taylor Otwell</strong> travaille sur un framework PHP personnel pour ses propres projets. Insatisfait des solutions existantes - CodeIgniter en tête - il publie discrètement la première version publique de Laravel sur GitHub. Personne ne se doute que cette sortie discothèque va transformer radicalement l'écosystème PHP mondial.</p>

<h2>Les origines : frustration et pragmatisme</h2>

<p>À l'époque, CodeIgniter est le framework PHP dominant. Il est simple, bien documenté, mais il manque de fonctionnalités modernes : pas d'injection de dépendances native, pas d'ORM élégant, pas de système d'authentification intégré. Taylor Otwell veut quelque chose de plus expressif, plus agréable à utiliser au quotidien.</p>

<p>Sa philosophie de départ est simple : <em>le code devrait être beau à lire</em>. Un développeur ne devrait pas se battre contre son framework - il devrait le ressentir comme un allié.</p>

<h2>Laravel 1 et 2 (2011) : les premières briques</h2>

<p>Les deux premières versions posent les fondations : routage, vues, sessions, authentification de base. Elles sont fonctionnelles mais encore limitées. La communauté naissante est enthousiaste mais réduite.</p>

<h2>Laravel 3 (2012) : le tournant</h2>

<p>Laravel 3 introduit les <strong>Artisan CLI</strong>, le système de <strong>bundles</strong> (prédécesseur des packages), et surtout une documentation soignée qui attire des milliers de nouveaux utilisateurs. C'est la version qui fait réellement décoller Laravel sur la scène internationale.</p>

<h2>Laravel 4 (2013) : la reconstruction sur Composer</h2>

<p>Une refonte complète. Laravel adopte <strong>Composer</strong> comme gestionnaire de dépendances, ce qui le rend modulaire et extensible à l'infini. Eloquent ORM devient ce que les développeurs connaissent aujourd'hui : une interface élégante pour interagir avec les bases de données relationnelles. Laravel 4 est un framework mature.</p>

<h2>Laravel 5 à 8 (2015-2021) : maturité et écosystème</h2>

<p>Ces versions consolident Laravel comme le framework PHP numéro un dans le monde. Des outils compagnons voient le jour : <strong>Forge</strong> (déploiement), <strong>Envoyer</strong> (zero-downtime deploys), <strong>Nova</strong> (admin panel), <strong>Vapor</strong> (serverless sur AWS). Chaque version apporte des améliorations majeures - middleware, broadcasting, task scheduling, Telescope, Sanctum, Breeze.</p>

<h2>Laravel 9 à 11 (2022-2024) : modernisation PHP</h2>

<p>Laravel suit de près l'évolution de PHP. PHP 8 apporte les types union, les enums natifs, les attributs - Laravel les adopte rapidement. L'architecture s'affine, le code devient plus typé, plus expressif. Livewire et Filament émergent comme standards de facto pour les interfaces dynamiques et les panneaux d'administration.</p>

<h2>Laravel 12 et 13 (2025-2026) : la génération IA</h2>

<p>Les dernières versions intègrent nativement le support des LLM, des outils pour construire des applications AI-native, et une compatibilité PHP 8.3+ obligatoire. <strong>C'est cette version que nous utilisons chez Laravel CI</strong> pour construire cette plateforme communautaire.</p>

<h2>Laravel vu depuis Abidjan</h2>

<p>Pour nous, développeurs ivoiriens, Laravel n'est pas qu'un framework - c'est la porte d'entrée vers le développement web professionnel. Son approche pédagogique, sa documentation exemplaire et son écosystème riche en font l'outil idéal pour apprendre à construire des applications sérieuses.</p>

<p>C'est pourquoi Laravel CI existe : pour que chaque développeur ivoirien puisse maîtriser cet outil, trouver du travail grâce à lui, et contribuer à l'écosystème mondial depuis notre continent.</p>

<p><em>Bienvenue dans la communauté.</em></p>
HTML;
    }
}
