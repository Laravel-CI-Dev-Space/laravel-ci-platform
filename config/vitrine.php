<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Vitrine / marketing site
    |--------------------------------------------------------------------------
    |
    | Default SEO metadata and social links used by the public showcase pages
    | (home, about, join, …). Values fall back to these defaults when a page
    | does not define its own title/description.
    |
    */

    'default_title' => "Laravel CI — La communauté Laravel de Côte d'Ivoire",

    'default_description' => "Rejoins 900+ développeurs Laravel ivoiriens. Forum technique, blog, événements, offres d'emploi : la communauté tech africaine de référence.",

    'og_image' => 'assets/web/img/logo.png',

    'social' => [
        'github' => env('VITRINE_GITHUB_URL', 'https://github.com/Laravel-CI-Dev-Space/laravel-ci'),
        'linkedin' => env('VITRINE_LINKEDIN_URL'),
        'whatsapp' => env('VITRINE_WHATSAPP_URL'),
        'twitter' => env('VITRINE_TWITTER_URL'),
    ],

];
