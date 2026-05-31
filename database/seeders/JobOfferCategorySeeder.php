<?php

namespace Database\Seeders;

use App\Models\JobOfferCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JobOfferCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Backend Development',   'icon' => 'heroicon-o-server'],
            ['name' => 'Frontend Development',  'icon' => 'heroicon-o-computer-desktop'],
            ['name' => 'Full Stack',             'icon' => 'heroicon-o-code-bracket'],
            ['name' => 'Mobile Development',    'icon' => 'heroicon-o-device-phone-mobile'],
            ['name' => 'DevOps & Cloud',        'icon' => 'heroicon-o-cloud'],
            ['name' => 'Data Science & AI',     'icon' => 'heroicon-o-chart-bar'],
            ['name' => 'UI/UX Design',          'icon' => 'heroicon-o-paint-brush'],
            ['name' => 'Project Management',    'icon' => 'heroicon-o-clipboard-document-list'],
            ['name' => 'QA & Testing',          'icon' => 'heroicon-o-bug-ant'],
            ['name' => 'Cybersecurity',         'icon' => 'heroicon-o-shield-check'],
        ];

        foreach ($categories as $category) {
            JobOfferCategory::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'icon' => $category['icon'],
                ]
            );
        }

        $this->command->info('✅ Job offer categories seeded.');
    }
}
