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
        // Super-admin : Wilson Kouassi
        // Retrouvé par github_id : l'email peut varier selon la config OAuth GitHub
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
                'bio'              => "Lead Developer - Laravel Côte d'Ivoire. Passionné de PHP et Laravel.",
                'laravel_level'    => 'expert',
                'years_experience' => '5_10_ans',
                'tech_stack'       => ['Laravel', 'PHP', 'Livewire', 'Filament', 'Vue.js', 'MySQL', 'Docker'],
                'academic_level'   => 'master_ingenieur',
                'job_status'       => 'en_fonction',
                'portfolio_url'    => 'https://github.com/Ky-Wilson',
            ]
        );

        $this->command->info('Users seedés : 1 super-admin (Wilson Kouassi).');
    }
}
