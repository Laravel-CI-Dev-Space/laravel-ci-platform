<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Runs all seeders in the correct order.
     * RoleSeeder must always run first so users can be assigned roles.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,           // 1. Roles + permissions
            TagSeeder::class,            // 2. Tags forum/blog
            JobCategorySeeder::class,      // 3. Job categories (table job_categories)
            JobSkillSeeder::class,       // 4. Job skills
            UserSeeder::class,           // 5. Users (après roles)
            VitrineSettingSeeder::class, // 6. Contenu dynamique des pages vitrine
        ]);

        $this->command->info('');
        $this->command->info('🇨🇮 Laravel CI — Database seeded successfully!');
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
