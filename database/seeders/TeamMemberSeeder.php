<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'first_name'      => 'Wilson',
                'last_name'       => 'Kouassi',
                'role'            => 'Lead & Co-fondateur',
                'avatar'          => 'fondateurs/wilson.jpeg',
                'avatar_initials' => 'WK',
                'avatar_color'    => 'av-1',
                'github_url'      => 'https://github.com/Ky-Wilson',
                'linkedin_url'    => null,
                'twitter_url'     => null,
                'bio'             => "Co-fondateur de Laravel CI et architecte de la plateforme. Développeur passionné, il a posé les bases techniques de la communauté avec la conviction que les développeurs ivoiriens méritent un espace de premier plan.",
                'order'           => 1,
                'is_active'       => true,
            ],
            [
                'first_name'      => 'Mahamadou',
                'last_name'       => 'Diaby',
                'role'            => 'Lead & Co-fondateur',
                'avatar'          => 'fondateurs/Mahamadou.jpeg',
                'avatar_initials' => 'MD',
                'avatar_color'    => 'av-2',
                'github_url'      => 'https://github.com/',
                'linkedin_url'    => null,
                'twitter_url'     => null,
                'bio'             => "Co-fondateur de Laravel CI et pilier de la communauté. Fort de son expertise en développement Laravel, il co-dirige la vision de la plateforme et anime les échanges au sein de l'écosystème ivoirien.",
                'order'           => 2,
                'is_active'       => true,
            ],
        ];

        foreach ($members as $member) {
            TeamMember::updateOrCreate(
                ['first_name' => $member['first_name'], 'last_name' => $member['last_name']],
                $member
            );
        }
    }
}
