<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── GENERAL ──────────────────────────────────────────────
            ['key' => 'site_name',        'group' => 'general', 'type' => 'text',     'label' => 'Nom du site',               'value' => "Laravel Côte d'Ivoire",                                                                                         'order' => 1],
            ['key' => 'site_tagline',     'group' => 'general', 'type' => 'text',     'label' => 'Tagline',                   'value' => "La communauté Laravel de Côte d'Ivoire",                                                                         'order' => 2],
            ['key' => 'site_description', 'group' => 'general', 'type' => 'textarea', 'label' => 'Description',               'value' => "La première communauté structurée dédiée aux développeurs Laravel en Côte d'Ivoire.",                            'order' => 3],
            ['key' => 'site_email',       'group' => 'general', 'type' => 'text',     'label' => 'Email de contact',          'value' => 'contact@laravelci.com',                                                                                          'order' => 4],

            // ── SOCIAL ───────────────────────────────────────────────
            ['key' => 'social_github',   'group' => 'social', 'type' => 'url', 'label' => 'GitHub',                'value' => 'https://github.com/Laravel-CI-Dev-Space', 'order' => 1],
            ['key' => 'social_linkedin', 'group' => 'social', 'type' => 'url', 'label' => 'LinkedIn',              'value' => '',                                         'order' => 2],
            ['key' => 'social_twitter',  'group' => 'social', 'type' => 'url', 'label' => 'Twitter / X',           'value' => '',                                         'order' => 3],
            ['key' => 'social_whatsapp', 'group' => 'social', 'type' => 'url', 'label' => 'WhatsApp (lien groupe)', 'value' => '',                                         'order' => 4],

            // ── HOME ─────────────────────────────────────────────────
            ['key' => 'home_hero_badge',          'group' => 'home', 'type' => 'text',     'label' => 'Hero badge text',           'value' => 'Laravel 13 · PHP 8.3 · Open source',                                                                                'order' => 1],
            ['key' => 'home_hero_title',          'group' => 'home', 'type' => 'text',     'label' => 'Hero titre principal',      'value' => "La communauté Laravel de Côte d'Ivoire",                                                                            'order' => 2],
            ['key' => 'home_hero_subtitle',       'group' => 'home', 'type' => 'textarea', 'label' => 'Hero sous-titre',           'value' => 'Rejoins 1 100+ développeurs ivoiriens, partage, apprends et grandis avec la communauté.',                        'order' => 3],
            ['key' => 'home_cta_primary_label',   'group' => 'home', 'type' => 'text',     'label' => 'CTA principal label',       'value' => 'Rejoindre la communauté',                                                                                          'order' => 4],
            ['key' => 'home_cta_secondary_label', 'group' => 'home', 'type' => 'text',     'label' => 'CTA secondaire label',      'value' => 'Accéder au forum',                                                                                                  'order' => 5],
            ['key' => 'home_questions_preview',   'group' => 'home', 'type' => 'number',   'label' => "Nb questions page d'accueil", 'value' => '3',                                                                                                               'order' => 6],
            ['key' => 'home_articles_preview',    'group' => 'home', 'type' => 'number',   'label' => "Nb articles page d'accueil",  'value' => '3',                                                                                                               'order' => 7],
            ['key' => 'home_events_preview',      'group' => 'home', 'type' => 'number',   'label' => "Nb events page d'accueil",    'value' => '3',                                                                                                               'order' => 8],
            ['key' => 'home_cta_banner_title',    'group' => 'home', 'type' => 'text',     'label' => 'Bannière CTA titre',        'value' => "Prêt à construire l'avenir de la tech ivoirienne ?",                                                                'order' => 9],
            ['key' => 'home_cta_banner_text',     'group' => 'home', 'type' => 'textarea', 'label' => 'Bannière CTA texte',        'value' => 'Connecte-toi avec GitHub, pose ta première question et rejoins 1 100+ développeurs qui progressent ensemble.',     'order' => 10],

            // ── ABOUT ────────────────────────────────────────────────
            ['key' => 'about_hero_title',    'group' => 'about', 'type' => 'text',     'label' => 'About hero titre',      'value' => "La première communauté structurée de développeurs Laravel en Côte d'Ivoire",          'order' => 1],
            ['key' => 'about_hero_subtitle', 'group' => 'about', 'type' => 'textarea', 'label' => 'About hero sous-titre', 'value' => "Laravel CI est née de la conviction qu'un développeur ivoirien ne devrait pas avancer seul. Fondée sur le partage des connaissances, l'inclusion et la croissance collective.", 'order' => 2],
            ['key' => 'about_mission',       'group' => 'about', 'type' => 'textarea', 'label' => 'Mission',               'value' => "Faire de Laravel CI un espace structuré, inclusif et actif pour tous les développeurs Laravel de Côte d'Ivoire.", 'order' => 3],
            ['key' => 'about_vision',        'group' => 'about', 'type' => 'textarea', 'label' => 'Vision',                'value' => "Faire de Laravel Côte d'Ivoire la communauté de développeurs Laravel la plus active, la plus inclusive et la plus influente d'Afrique de l'Ouest, en contribuant à l'excellence technique et à l'employabilité des développeurs ivoiriens à l'échelle locale et internationale.", 'order' => 4],
            ['key' => 'about_cta_title',     'group' => 'about', 'type' => 'text',     'label' => 'About CTA titre',       'value' => "Ta place dans la communauté t'attend",                                              'order' => 5],
            ['key' => 'about_cta_text',      'group' => 'about', 'type' => 'textarea', 'label' => 'About CTA texte',       'value' => "1 100+ développeurs ivoiriens construisent l'avenir de la tech africaine, un commit à la fois.", 'order' => 6],

            // ── SEO ──────────────────────────────────────────────────
            ['key' => 'seo_home_title',       'group' => 'seo', 'type' => 'text',     'label' => "SEO titre d'accueil",       'value' => "Laravel CI - La communauté Laravel de Côte d'Ivoire", 'order' => 1],
            ['key' => 'seo_home_description', 'group' => 'seo', 'type' => 'textarea', 'label' => "SEO description d'accueil", 'value' => "Rejoins 1 100+ développeurs Laravel ivoiriens. Forum, blog, événements, emplois : la communauté tech africaine de référence.", 'order' => 2],
            ['key' => 'seo_og_image',         'group' => 'seo', 'type' => 'image',    'label' => 'OG Image (1200x630)',       'value' => '', 'order' => 3],

            // ── IDENTITY (logos, brand) ───────────────────────────────
            ['key' => 'identity_brand_name',    'group' => 'identity', 'type' => 'text',  'label' => 'Nom du brand (header)',        'value' => 'Laravel CI',                          'order' => 1],
            ['key' => 'identity_logo_mark',     'group' => 'identity', 'type' => 'image', 'label' => 'Logo mark (icône)',            'value' => 'web/img/logo-mark.png',               'order' => 2],
            ['key' => 'identity_logo_full',     'group' => 'identity', 'type' => 'image', 'label' => 'Logo complet (optionnel)',     'value' => '',                                    'order' => 3],
            ['key' => 'identity_favicon',       'group' => 'identity', 'type' => 'image', 'label' => 'Favicon',                     'value' => 'web/img/favicon.png',                 'order' => 4],
            ['key' => 'identity_header_cta',    'group' => 'identity', 'type' => 'text',  'label' => 'Bouton header (non connecté)', 'value' => 'Se connecter avec GitHub',            'order' => 5],

            // ── FOOTER ───────────────────────────────────────────────
            ['key' => 'footer_tagline',              'group' => 'footer', 'type' => 'textarea', 'label' => 'Description / tagline',        'value' => "La première communauté structurée de développeurs Laravel en Côte d'Ivoire et dans la diaspora ivoirienne. Excellence technique africaine, ensemble.", 'order' => 1],
            ['key' => 'footer_col1_title',           'group' => 'footer', 'type' => 'text',     'label' => 'Colonne 2 - titre',            'value' => 'Navigation',                       'order' => 2],
            ['key' => 'footer_col2_title',           'group' => 'footer', 'type' => 'text',     'label' => 'Colonne 3 - titre',            'value' => 'Communauté',                       'order' => 3],
            ['key' => 'footer_col3_title',           'group' => 'footer', 'type' => 'text',     'label' => 'Colonne 4 - titre',            'value' => 'Contact',                          'order' => 4],
            ['key' => 'footer_contact_location',     'group' => 'footer', 'type' => 'text',     'label' => 'Localisation',                 'value' => "Abidjan, Côte d'Ivoire",            'order' => 5],
            ['key' => 'footer_contact_email',        'group' => 'footer', 'type' => 'text',     'label' => 'Email de contact',             'value' => 'hello@laravel.ci',                 'order' => 6],
            ['key' => 'footer_whatsapp_label',       'group' => 'footer', 'type' => 'text',     'label' => 'Label lien WhatsApp',          'value' => 'Rejoindre le groupe WhatsApp',     'order' => 7],
            ['key' => 'footer_github_label',         'group' => 'footer', 'type' => 'text',     'label' => 'Label lien GitHub',            'value' => 'Contribuer sur GitHub',            'order' => 8],
            ['key' => 'footer_code_of_conduct_url',  'group' => 'footer', 'type' => 'url',      'label' => 'URL Code de conduite',         'value' => '',                                 'order' => 9],
            ['key' => 'footer_copyright',            'group' => 'footer', 'type' => 'text',     'label' => 'Texte copyright',              'value' => "Laravel Côte d'Ivoire · MIT License", 'order' => 10],
            ['key' => 'footer_built_with',           'group' => 'footer', 'type' => 'text',     'label' => 'Texte "Built with"',           'value' => "Construit avec passion en Côte d'Ivoire", 'order' => 11],

            // ── NOTIFICATIONS ────────────────────────────────────────
            ['key' => 'job_alert_frequency', 'group' => 'notifications', 'type' => 'frequency', 'label' => 'Fréquence des alertes emploi', 'value' => 'weekly', 'order' => 1],

            // ── RÉPUTATION & GRADES ──────────────────────────────────
            ['key' => 'reputation_points_question',         'group' => 'reputation', 'type' => 'number',    'label' => 'Points par question publiée',          'value' => '5',  'order' => 1],
            ['key' => 'reputation_points_answer',            'group' => 'reputation', 'type' => 'number',    'label' => 'Points par réponse postée',            'value' => '10', 'order' => 2],
            ['key' => 'reputation_points_accepted_answer',   'group' => 'reputation', 'type' => 'number',    'label' => 'Bonus réponse acceptée',               'value' => '20', 'order' => 3],
            ['key' => 'reputation_points_article',           'group' => 'reputation', 'type' => 'number',    'label' => 'Points par article publié',            'value' => '25', 'order' => 4],
            ['key' => 'reputation_points_vote',              'group' => 'reputation', 'type' => 'number',    'label' => 'Points par vote reçu',                 'value' => '2',  'order' => 5],
            ['key' => 'reputation_points_comment',           'group' => 'reputation', 'type' => 'number',    'label' => 'Points par commentaire',               'value' => '1',  'order' => 6],
            ['key' => 'reputation_points_event',             'group' => 'reputation', 'type' => 'number',    'label' => 'Points par participation à un événement', 'value' => '5',  'order' => 7],
            ['key' => 'reputation_points_seniority_month',   'group' => 'reputation', 'type' => 'number',    'label' => 'Points par mois d\'ancienneté',        'value' => '2',  'order' => 8],
            ['key' => 'reputation_max_seniority_months',     'group' => 'reputation', 'type' => 'number',    'label' => 'Plafond ancienneté (mois)',            'value' => '25', 'order' => 9],
            ['key' => 'reputation_recalculate_frequency',    'group' => 'reputation', 'type' => 'frequency', 'label' => 'Fréquence de recalcul des grades',     'value' => 'monthly', 'order' => 10],

            // ── CARTES MEMBRE ────────────────────────────────────────
            ['key' => 'card_level_1_points', 'group' => 'member_cards', 'type' => 'number', 'label' => 'Réputation min. - Carte Initié (niveau 1)',       'value' => '300', 'order' => 1],
            ['key' => 'card_level_2_points', 'group' => 'member_cards', 'type' => 'number', 'label' => 'Réputation min. - Carte Bâtisseur (niveau 2)',    'value' => '600', 'order' => 2],
            ['key' => 'card_level_3_points', 'group' => 'member_cards', 'type' => 'number', 'label' => 'Réputation min. - Carte Maître Artisan (niveau 3)', 'value' => '900', 'order' => 3],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                array_merge($setting, ['description' => null])
            );
        }
    }
}
