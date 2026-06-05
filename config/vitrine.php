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

    'default_title' => 'Laravel CI — The Laravel Community of Côte d\'Ivoire',

    'default_description' => 'Join 500+ Ivorian Laravel & PHP developers. Share knowledge, find jobs, attend events, and grow together in Abidjan and across the diaspora.',

    'og_image' => 'assets/web/img/logo.png',

    'social' => [
        'github' => env('VITRINE_GITHUB_URL', 'https://github.com/Laravel-CI-Dev-Space/laravel-ci'),
        'linkedin' => env('VITRINE_LINKEDIN_URL'),
        'whatsapp' => env('VITRINE_WHATSAPP_URL'),
        'twitter' => env('VITRINE_TWITTER_URL'),
    ],

];
