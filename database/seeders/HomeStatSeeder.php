<?php

namespace Database\Seeders;

use App\Models\HomeStat;
use Illuminate\Database\Seeder;

class HomeStatSeeder extends Seeder
{
    public function run(): void
    {
        $stats = [
            ['icon' => 'fa-solid fa-users',           'label' => 'Members',   'value' => 500,  'suffix' => '+', 'auto_count' => true, 'model' => 'App\\Models\\User',     'order' => 1],
            ['icon' => 'fa-solid fa-circle-question', 'label' => 'Questions', 'value' => 1200, 'suffix' => '+', 'auto_count' => true, 'model' => 'App\\Models\\Question', 'order' => 2],
            ['icon' => 'fa-solid fa-calendar-check',  'label' => 'Events',    'value' => 24,   'suffix' => '+', 'auto_count' => true, 'model' => 'App\\Models\\Event',    'order' => 3],
            ['icon' => 'fa-solid fa-book-open',       'label' => 'Articles',  'value' => 80,   'suffix' => '+', 'auto_count' => true, 'model' => 'App\\Models\\Article',  'order' => 4],
        ];

        foreach ($stats as $stat) {
            HomeStat::updateOrCreate(
                ['label' => $stat['label']],
                array_merge($stat, ['is_active' => true])
            );
        }
    }
}
