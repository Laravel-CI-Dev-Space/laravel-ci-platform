<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Seuils de réputation pour débloquer chaque niveau de carte
    |--------------------------------------------------------------------------
    | Ces valeurs sont des fallbacks. Les valeurs live viennent de SiteSettings
    | (clés : card_level_1_points, card_level_2_points, card_level_3_points).
    */
    'thresholds' => [
        1 => 300,
        2 => 600,
        3 => 900,
    ],

    /*
    |--------------------------------------------------------------------------
    | Noms des niveaux (affichés sur la carte)
    |--------------------------------------------------------------------------
    */
    'level_names' => [
        1 => 'Initié',
        2 => 'Bâtisseur',
        3 => 'Maître Artisan',
    ],

    /*
    |--------------------------------------------------------------------------
    | Templates Blade par niveau
    |--------------------------------------------------------------------------
    | Pour changer un design, il suffit de pointer vers une autre vue.
    | La vue reçoit une variable $card (MemberCard avec relations chargées).
    */
    'templates' => [
        1 => 'member-card.templates.level-1',
        2 => 'member-card.templates.level-2',
        3 => 'member-card.templates.level-3',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dimensions d'export PNG
    |--------------------------------------------------------------------------
    */
    'width'  => 800,
    'height' => 450,

    /*
    |--------------------------------------------------------------------------
    | Dossier de stockage des QR codes (storage/app/public/...)
    |--------------------------------------------------------------------------
    */
    'qr_disk' => 'public',
    'qr_path' => 'member-cards/qr',

];
