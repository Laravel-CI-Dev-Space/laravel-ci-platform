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
                'first_name'      => 'Serge',
                'last_name'       => 'Brou',
                'role'            => 'Founder & Architect',
                'avatar_initials' => 'SB',
                'avatar_color'    => 'av-1',
                'github_url'      => 'https://github.com/',
                'linkedin_url'    => null,
                'twitter_url'     => null,
                'bio'             => 'Fondateur de Laravel CI et architecte de la plateforme. Passionné de Laravel depuis la version 5, il a lancé la communauté avec la conviction que les développeurs ivoiriens méritent un espace structuré.',
                'order'           => 1,
            ],
            [
                'first_name'      => 'Fatou',
                'last_name'       => 'Diallo',
                'role'            => 'Community Lead',
                'avatar_initials' => 'FD',
                'avatar_color'    => 'av-3',
                'github_url'      => 'https://github.com/',
                'linkedin_url'    => null,
                'twitter_url'     => null,
                'bio'             => "Responsable communauté, elle coordonne les événements, les partenariats et l'animation du forum.",
                'order'           => 2,
            ],
            [
                'first_name'      => 'Aïcha',
                'last_name'       => 'Doumbia',
                'role'            => 'Content & Events',
                'avatar_initials' => 'AD',
                'avatar_color'    => 'av-5',
                'github_url'      => 'https://github.com/',
                'linkedin_url'    => null,
                'twitter_url'     => null,
                'bio'             => 'Responsable contenu et événements, elle produit les articles, organise les meetups et webinaires.',
                'order'           => 3,
            ],
            [
                'first_name'      => 'Yao',
                'last_name'       => 'Térence',
                'role'            => 'Open Source Maintainer',
                'avatar_initials' => 'YT',
                'avatar_color'    => 'av-4',
                'github_url'      => 'https://github.com/',
                'linkedin_url'    => null,
                'twitter_url'     => null,
                'bio'             => 'Mainteneur open source, il supervise les contributions, les pull requests et la qualité du code de la plateforme.',
                'order'           => 4,
            ],
        ];

        foreach ($members as $member) {
            TeamMember::updateOrCreate(
                ['first_name' => $member['first_name'], 'last_name' => $member['last_name']],
                array_merge($member, ['avatar' => null, 'is_active' => true])
            );
        }
    }
}
