<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ══════════════════════════════════════════════════════════
        //  SUPER-ADMIN UNIQUE — yanne.kouassi@epitech.eu
        //  syncRoles() garantit qu'il est le SEUL super-admin seedé
        //  et qu'un re-seed ne duplique pas le rôle.
        // ══════════════════════════════════════════════════════════
        // Recherche par github_id car le compte existe déjà via OAuth
        // (email peut être null ou différent dans la colonne selon la config GitHub)
        $superAdmin = User::updateOrCreate(
            ['github_id' => '167759591'],
            [
                'name'              => 'Wilson Kouassi',
                'email'             => 'yanne.kouassi@epitech.eu',
                'github_username'   => 'Ky-Wilson',
                'avatar'            => 'https://avatars.githubusercontent.com/u/167759591',
                'is_active'         => true,
                'email_verified_at' => now(),
                'last_login_at'     => now(),
            ]
        );
        $superAdmin->syncRoles([UserRole::SuperAdmin->value]);

        Profile::firstOrCreate(
            ['user_id' => $superAdmin->id],
            [
                'country'          => "Côte d'Ivoire",
                'city'             => 'Abidjan',
                'district'         => 'Cocody',
                'bio'              => "Lead Developer — Laravel Côte d'Ivoire. Passionné de PHP et Laravel.",
                'laravel_level'    => 'expert',
                'years_experience' => '5_10_ans',
                'tech_stack'       => ['Laravel', 'PHP', 'Livewire', 'Filament', 'Vue.js', 'MySQL', 'Docker'],
                'academic_level'   => 'master_ingenieur',
                'job_status'       => 'en_fonction',
                'portfolio_url'    => 'https://github.com/Ky-Wilson',
            ]
        );

        // ──────────────────────────────────────────────────────────
        //  COMPTES DE TEST (développement uniquement)
        // ──────────────────────────────────────────────────────────

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
        $admin->syncRoles([UserRole::Admin->value]);

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
        $moderator->syncRoles([UserRole::Moderator->value]);

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
        $member->syncRoles([UserRole::Member->value]);

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
        $suspended->syncRoles([UserRole::Member->value]);

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
        $banned->syncRoles([UserRole::Member->value]);

        $this->command->info('✅ Users seedés (1 super-admin + 5 comptes de test).');
        $this->command->line('   Super-admin : yanne.kouassi@epitech.eu');
    }
}
