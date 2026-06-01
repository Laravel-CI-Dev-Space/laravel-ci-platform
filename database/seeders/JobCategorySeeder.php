<?php

namespace Database\Seeders;

use App\Models\JobCategory;
use Illuminate\Database\Seeder;

class JobCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Développement backend', 'slug' => 'backend'],
            ['name' => 'Développement frontend', 'slug' => 'frontend'],
            ['name' => 'Full stack', 'slug' => 'full-stack'],
            ['name' => 'DevOps', 'slug' => 'devops'],
            ['name' => 'Mobile', 'slug' => 'mobile'],
            ['name' => 'QA & tests', 'slug' => 'qa'],
        ];

        foreach ($categories as $category) {
            JobCategory::firstOrCreate(['slug' => $category['slug']], $category);
        }

        $this->command->info('✅ Catégories emploi créées (' . count($categories) . ').');
    }
}
