<?php

namespace Database\Seeders;

use App\Models\HomeStat;
use Illuminate\Database\Seeder;

class HomeStatSeeder extends Seeder
{
    public function run(): void
    {
        $stats = [
            ['icon' => 'fa-solid fa-users',           'label' => 'Members',          'value' => 0,    'suffix' => '+', 'auto_count' => true,  'model' => 'App\\Models\\User',     'order' => 0],
            ['icon' => 'fa-brands fa-linkedin',       'label' => 'Membres LinkedIn', 'value' => 900,  'suffix' => '+', 'auto_count' => false, 'model' => null,                    'order' => 1],
            ['icon' => 'fa-brands fa-whatsapp',       'label' => 'Membres WhatsApp', 'value' => 340,  'suffix' => '+', 'auto_count' => false, 'model' => null,                    'order' => 2],
            ['icon' => 'fa-solid fa-calendar-check',  'label' => 'Événements',       'value' => 10,   'suffix' => '+', 'auto_count' => true,  'model' => 'App\\Models\\Event',    'order' => 3],
            ['icon' => 'fa-solid fa-book-open',       'label' => 'Articles',         'value' => 2,    'suffix' => '+', 'auto_count' => true,  'model' => 'App\\Models\\Article',  'order' => 4],
        ];

        foreach ($stats as $stat) {
            HomeStat::updateOrCreate(
                ['label' => $stat['label']],
                array_merge($stat, ['is_active' => true])
            );
        }
    }
}
