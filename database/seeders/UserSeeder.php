<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'wilson@laravelci.com'],
            [
                'name'              => 'Wilson Kouassi',
                'github_id'         => '167759591',
                'github_username'   => 'Ky-Wilson',
                'avatar'            => 'https://avatars.githubusercontent.com/u/167759591',
                'is_active'         => true,
                'email_verified_at' => now(),
                'last_login_at'     => now(),
            ]
        );
        $superAdmin->assignRole('super-admin');

        Profile::firstOrCreate(
            ['user_id' => $superAdmin->id],
            [
                'country'          => "Côte d'Ivoire",
                'city'             => 'Abidjan',
                'district'         => 'Cocody',
                'bio'              => 'Lead Developer — Laravel Côte d\'Ivoire. Passionné de PHP et Laravel.',
                'laravel_level'    => 'expert',
                'years_experience' => '5_10_ans',
                'tech_stack'       => ['Laravel', 'PHP', 'Livewire', 'Filament', 'Vue.js', 'MySQL', 'Docker'],
                'academic_level'   => 'master_ingenieur',
                'job_status'       => 'en_fonction',
                'portfolio_url'    => 'https://github.com/Ky-Wilson',
            ]
        );

        // Test Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@laravelci.com'],
            [
                'name'              => 'Admin Test',
                'github_id'         => '11111111',
                'github_username'   => 'admin-laravel-ci',
                'avatar'            => 'https://ui-avatars.com/api/?name=Admin&color=fff&background=FF6600',
                'is_active'         => true,
                'email_verified_at' => now(),
                'last_login_at'     => now(),
            ]
        );
        $admin->assignRole('admin');

        // Test Moderator
        $moderator = User::firstOrCreate(
            ['email' => 'moderator@laravelci.com'],
            [
                'name'              => 'Moderator Test',
                'github_id'         => '22222222',
                'github_username'   => 'mod-laravel-ci',
                'avatar'            => 'https://ui-avatars.com/api/?name=Mod&color=fff&background=1C1C2E',
                'is_active'         => true,
                'email_verified_at' => now(),
                'last_login_at'     => now(),
            ]
        );
        $moderator->assignRole('moderator');

        // Test Member
        $member = User::firstOrCreate(
            ['email' => 'member@laravelci.com'],
            [
                'name'              => 'Member Test',
                'github_id'         => '33333333',
                'github_username'   => 'member-laravel-ci',
                'avatar'            => 'https://ui-avatars.com/api/?name=Member&color=fff&background=2ECC71',
                'is_active'         => true,
                'email_verified_at' => now(),
                'last_login_at'     => now(),
            ]
        );
        $member->assignRole('member');

        // Suspended member (test)
        $suspended = User::firstOrCreate(
            ['email' => 'suspended@laravelci.com'],
            [
                'name'              => 'Suspended Test',
                'github_id'         => '44444444',
                'github_username'   => 'suspended-laravel-ci',
                'avatar'            => 'https://ui-avatars.com/api/?name=Suspended&color=fff&background=E74C3C',
                'is_active'         => true,
                'suspended_until'   => now()->addDays(7),
                'email_verified_at' => now(),
            ]
        );
        $suspended->assignRole('member');

        // Banned member (test)
        $banned = User::firstOrCreate(
            ['email' => 'banned@laravelci.com'],
            [
                'name'              => 'Banned Test',
                'github_id'         => '55555555',
                'github_username'   => 'banned-laravel-ci',
                'avatar'            => 'https://ui-avatars.com/api/?name=Banned&color=fff&background=7F8C8D',
                'is_active'         => false,
                'email_verified_at' => now(),
            ]
        );
        $banned->assignRole('member');

        // Ibrahima DIARRA — Admin Filament + QA
        $ibrahima = User::firstOrCreate(
            ['email' => 'ibrahima@laravelci.com'],
            [
                'name'              => 'Ibrahima DIARRA',
                'github_id'         => '157432707',
                'github_username'   => 'DiarraIbra',
                'avatar'            => 'https://avatars.githubusercontent.com/u/157432707',
                'is_active'         => true,
                'email_verified_at' => now(),
                'last_login_at'     => now(),
            ]
        );
        $ibrahima->assignRole('admin');

        Profile::firstOrCreate(
            ['user_id' => $ibrahima->id],
            [
                'country'          => "Côte d'Ivoire",
                'city'             => 'Abidjan',
                'bio'              => 'Admin Filament & QA cross-modules — Laravel Côte d\'Ivoire.',
                'laravel_level'    => 'intermediaire',
                'years_experience' => '1_3_ans',
                'tech_stack'       => ['Laravel', 'PHP', 'Filament', 'MySQL'],
                'job_status'       => 'en_recherche',
                'portfolio_url'    => 'https://github.com/DiarraIbra',
            ]
        );

        $this->command->info('✅ Users seeded (6 users : super-admin, admin, moderator, member, suspended, banned).');
    }
}
