<?php

namespace Database\Seeders;

use App\Models\JobSkill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JobSkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            'Laravel', 'PHP', 'Livewire', 'Filament', 'Vue.js', 'React',
            'Next.js', 'Nuxt.js', 'TypeScript', 'JavaScript', 'Python',
            'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'Docker', 'Linux',
            'Git', 'AWS', 'TailwindCSS', 'Bootstrap', 'REST API', 'GraphQL',
            'Nginx', 'Node.js', 'Flutter', 'Dart', 'Kotlin', 'Swift',
        ];

        foreach ($skills as $skill) {
            JobSkill::firstOrCreate(
                ['slug' => Str::slug($skill)],
                ['name' => $skill]
            );
        }

        $this->command->info('✅ Job skills seeded (' . count($skills) . ' skills).');
    }
}
