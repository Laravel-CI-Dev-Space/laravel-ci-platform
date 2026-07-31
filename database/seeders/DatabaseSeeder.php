<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,               // 1. Roles + permissions
            TagSeeder::class,                // 2. Tags forum/blog
            JobOfferCategorySeeder::class,   // 3. Job categories
            JobSkillSeeder::class,           // 4. Job skills
            UserSeeder::class,               // 5. Users (après roles)
            PoleSeeder::class,               // 6. Pôles + membres (après users pour user_id)
            SiteSettingSeeder::class,        // 7. Paramètres du site
            HomeStatSeeder::class,           // 7. Statistiques page accueil
            PartnerSeeder::class,            // 8. Partenaires
            TeamMemberSeeder::class,         // 9. Co-fondateurs
            CommunityValueSeeder::class,     // 10. Valeurs communautaires
            TimelineEventSeeder::class,      // 11. Timeline Notre histoire
            AboutOriginSectionSeeder::class, // 12. Section Notre naissance
            GradeSeeder::class,              // 13. Grades de réputation
            ArticleSeeder::class,            // 14. Articles blog vitrine
        ]);

        if (config('database.default') === 'sqlite' && str_contains((string) config('database.connections.sqlite.database'), 'e2e')) {
            $this->call(E2eSeeder::class);
        }

        $this->command->info('');
        $this->command->info('Laravel CI - Database seeded successfully!');
        $this->command->info('');
        // $this->command->table(
        //     ['Role', 'Email', 'Password'],
        //     [
        //         ['super-admin', 'wilson@laravelci.com',    'GitHub OAuth'],
        //         ['admin',       'admin@laravelci.com',     'GitHub OAuth'],
        //         ['moderator',   'moderator@laravelci.com', 'GitHub OAuth'],
        //         ['member',      'member@laravelci.com',    'GitHub OAuth'],
        //         ['member (suspended)', 'suspended@laravelci.com', 'GitHub OAuth'],
        //         ['member (banned)',    'banned@laravelci.com',    'GitHub OAuth'],
        //     ]
        // );
    }
}
