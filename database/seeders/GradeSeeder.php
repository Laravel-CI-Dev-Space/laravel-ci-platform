<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Grade;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            ['name' => 'Recrue',            'min_points' => 0,     'color' => '#9CA3AF', 'icon' => 'fa-solid fa-seedling',           'description' => 'Bienvenue dans la communauté !'],
            ['name' => 'Apprenti',          'min_points' => 50,    'color' => '#60A5FA', 'icon' => 'fa-solid fa-graduation-cap',     'description' => 'Premiers pas dans la communauté.'],
            ['name' => 'Codeur',            'min_points' => 150,   'color' => '#3B82F6', 'icon' => 'fa-solid fa-code',               'description' => 'Membre actif et régulier.'],
            ['name' => 'Bâtisseur',         'min_points' => 350,   'color' => '#10B981', 'icon' => 'fa-solid fa-hammer',             'description' => 'Contribue à construire la communauté.'],
            ['name' => 'Artisan Laravel',   'min_points' => 700,   'color' => '#FF6600', 'icon' => 'fa-solid fa-screwdriver-wrench', 'description' => 'Maîtrise les bases avec sérieux.'],
            ['name' => 'Contributeur',      'min_points' => 1200,  'color' => '#8B5CF6', 'icon' => 'fa-solid fa-people-group',       'description' => 'Aide activement les autres membres.'],
            ['name' => 'Mentor',            'min_points' => 2000,  'color' => '#6366F1', 'icon' => 'fa-solid fa-chalkboard-user',    'description' => 'Guide et forme les nouveaux membres.'],
            ['name' => 'Expert Laravel',    'min_points' => 3200,  'color' => '#F59E0B', 'icon' => 'fa-solid fa-medal',              'description' => 'Expertise reconnue par la communauté.'],
            ['name' => 'Architecte',        'min_points' => 5000,  'color' => '#EC4899', 'icon' => 'fa-solid fa-compass-drafting',   'description' => 'Conçoit des solutions de référence.'],
            ['name' => 'Maître Artisan',    'min_points' => 8000,  'color' => '#DC2626', 'icon' => 'fa-solid fa-trophy',             'description' => 'Pilier incontournable de la communauté.'],
            ['name' => 'Légende du Code',   'min_points' => 13000, 'color' => '#7C3AED', 'icon' => 'fa-solid fa-fire',               'description' => 'Contribution exceptionnelle et durable.'],
            ['name' => 'Grand Maître',      'min_points' => 20000, 'color' => '#FFD700', 'icon' => 'fa-solid fa-crown',              'description' => 'Le plus haut grade de la communauté.'],
        ];

        foreach ($grades as $order => $grade) {
            Grade::updateOrCreate(
                ['slug' => Str::slug($grade['name'])],
                [
                    'name'        => $grade['name'],
                    'min_points'  => $grade['min_points'],
                    'color'       => $grade['color'],
                    'icon'        => $grade['icon'],
                    'description' => $grade['description'],
                    'order'       => $order + 1,
                ]
            );
        }
    }
}
