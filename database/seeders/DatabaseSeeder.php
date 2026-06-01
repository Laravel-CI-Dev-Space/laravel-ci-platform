<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,           // 1. Roles + permissions
            TagSeeder::class,            // 2. Tags forum/blog
            JobOfferCategorySeeder::class, // 3. Job categories
            JobSkillSeeder::class,       // 4. Job skills
            UserSeeder::class,           // 5. Users (après roles)
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
