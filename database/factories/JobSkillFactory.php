<?php

namespace Database\Factories;

use App\Models\JobSkill;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<JobSkill>
 */
class JobSkillFactory extends Factory
{
    protected $model = JobSkill::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Laravel', 'PHP', 'Filament', 'Livewire', 'Vue.js',
            'Tailwind CSS', 'MySQL', 'Docker', 'Git', 'Pest',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
