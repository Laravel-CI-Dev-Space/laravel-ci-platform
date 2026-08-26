<?php

namespace Database\Seeders;

use App\Models\HomeStat;
use Illuminate\Database\Seeder;

class HomeStatSeeder extends Seeder
{
    public function run(): void
    {
        // Nettoyage complet avant ré-insertion pour éviter les doublons
        // (labels anciens : "Membres LinkedIn", "Membres WhatsApp", "Members", etc.)
        HomeStat::truncate();

        $stats = [
            ['icon' => 'fa-solid fa-users',          'label' => 'Membres',    'value' => 1100, 'suffix' => '+', 'auto_count' => false, 'model' => null,                   'order' => 0],
            ['icon' => 'fa-solid fa-calendar-check', 'label' => 'Événements', 'value' => 12,   'suffix' => '+', 'auto_count' => true,  'model' => 'App\\Models\\Event',   'order' => 1],
            ['icon' => 'fa-solid fa-book-open',      'label' => 'Articles',   'value' => 2,    'suffix' => '+', 'auto_count' => true,  'model' => 'App\\Models\\Article', 'order' => 2],
        ];

        foreach ($stats as $stat) {
            HomeStat::create(array_merge($stat, ['is_active' => true]));
        }
    }
}
