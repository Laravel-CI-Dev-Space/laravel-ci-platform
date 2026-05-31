<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            // Forum + Both
            ['name' => 'Laravel',      'scope' => 'both',  'color' => '#FF2D20'],
            ['name' => 'PHP',          'scope' => 'both',  'color' => '#8892BE'],
            ['name' => 'Eloquent',     'scope' => 'forum', 'color' => '#F05340'],
            ['name' => 'Livewire',     'scope' => 'both',  'color' => '#FB70A9'],
            ['name' => 'Filament',     'scope' => 'both',  'color' => '#F59E0B'],
            ['name' => 'API',          'scope' => 'both',  'color' => '#3B82F6'],
            ['name' => 'Auth',         'scope' => 'forum', 'color' => '#8B5CF6'],
            ['name' => 'Deployment',   'scope' => 'forum', 'color' => '#10B981'],
            ['name' => 'Testing',      'scope' => 'both',  'color' => '#EF4444'],
            ['name' => 'Queue',        'scope' => 'forum', 'color' => '#F97316'],
            ['name' => 'Database',     'scope' => 'forum', 'color' => '#06B6D4'],
            ['name' => 'Performance',  'scope' => 'forum', 'color' => '#84CC16'],
            ['name' => 'Security',     'scope' => 'forum', 'color' => '#DC2626'],
            ['name' => 'Vue.js',       'scope' => 'both',  'color' => '#42B883'],
            ['name' => 'React',        'scope' => 'both',  'color' => '#61DAFB'],
            ['name' => 'TailwindCSS',  'scope' => 'both',  'color' => '#38BDF8'],
            ['name' => 'Docker',       'scope' => 'forum', 'color' => '#2496ED'],
            ['name' => 'Git',          'scope' => 'forum', 'color' => '#F05032'],
            ['name' => 'MySQL',        'scope' => 'forum', 'color' => '#4479A1'],
            ['name' => 'Redis',        'scope' => 'forum', 'color' => '#DC382D'],

            // Blog only
            ['name' => 'Tutorial',     'scope' => 'blog',  'color' => '#FF6600'],
            ['name' => 'Tips',         'scope' => 'blog',  'color' => '#2ECC71'],
            ['name' => 'Architecture', 'scope' => 'blog',  'color' => '#1C1C2E'],
            ['name' => 'Best Practices','scope' => 'blog', 'color' => '#9B59B6'],
            ['name' => 'News',         'scope' => 'blog',  'color' => '#E74C3C'],
            ['name' => 'Career',       'scope' => 'blog',  'color' => '#2980B9'],
            ['name' => 'Open Source',  'scope' => 'blog',  'color' => '#27AE60'],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['slug' => Str::slug($tag['name'])],
                [
                    'name'  => $tag['name'],
                    'scope' => $tag['scope'],
                    'color' => $tag['color'],
                ]
            );
        }

        $this->command->info('✅ Tags seeded (' . count($tags) . ' tags).');
    }
}
