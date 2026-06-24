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
                'icon'        => 'fa-solid fa-hands-holding-circle',
                'title'       => 'Partage',
                'description' => "La connaissance grandit quand on la partage. Ici, pas de rétention — chaque expérience partagée élève toute la communauté.",
                'order'       => 1,
            ],
            [
                'icon'        => 'fa-solid fa-heart',
                'title'       => 'Bienveillance',
                'description' => "Un espace sûr pour apprendre, poser des questions et progresser sans crainte du jugement. Toutes les questions méritent une réponse respectueuse.",
                'order'       => 2,
            ],
            [
                'icon'        => 'fa-solid fa-award',
                'title'       => 'Excellence',
                'description' => "Nous nous tenons mutuellement à un niveau élevé — du code propre, des pratiques solides, un vrai artisanat du développement.",
                'order'       => 3,
            ],
            [
                'icon'        => 'fa-solid fa-people-group',
                'title'       => 'Inclusion',
                'description' => "Chaque niveau, chaque parcours, chaque profil est bienvenu. Du débutant au senior, la communauté s'enrichit de sa diversité.",
                'order'       => 4,
            ],
            [
                'icon'        => 'fa-solid fa-seedling',
                'title'       => 'Impact local',
                'description' => "Nous construisons de la tech utile, ancrée dans la réalité ivoirienne, pour améliorer concrètement la vie de nos pairs et de nos utilisateurs.",
                'order'       => 5,
            ],
            [
                'icon'        => 'fa-solid fa-earth-africa',
                'title'       => 'Ouverture',
                'description' => "Connectés à l'écosystème Laravel mondial, nous travaillons avec les communautés partenaires pour apporter le meilleur de l'international à notre contexte local.",
                'order'       => 6,
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
