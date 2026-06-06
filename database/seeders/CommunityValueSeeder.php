<?php

namespace Database\Seeders;

use App\Models\CommunityValue;
use Illuminate\Database\Seeder;

class CommunityValueSeeder extends Seeder
{
    public function run(): void
    {
        $values = [
            [
                'icon'        => 'fa-solid fa-award',
                'title'       => 'Excellence',
                'description' => 'Nous nous tenons mutuellement à un niveau élevé — du code propre, un vrai artisanat.',
                'order'       => 1,
            ],
            [
                'icon'        => 'fa-solid fa-hands-holding-circle',
                'title'       => 'Partage',
                'description' => 'La connaissance grandit quand on la donne. Pas de rétention ici.',
                'order'       => 2,
            ],
            [
                'icon'        => 'fa-solid fa-people-group',
                'title'       => 'Inclusion',
                'description' => 'Chaque niveau, chaque parcours, chaque question est bienvenu.',
                'order'       => 3,
            ],
            [
                'icon'        => 'fa-solid fa-seedling',
                'title'       => 'Impact',
                'description' => "Nous construisons de la tech qui améliore la vie en Côte d'Ivoire.",
                'order'       => 4,
            ],
        ];

        foreach ($values as $value) {
            CommunityValue::updateOrCreate(
                ['title' => $value['title']],
                array_merge($value, ['is_active' => true])
            );
        }
    }
}
