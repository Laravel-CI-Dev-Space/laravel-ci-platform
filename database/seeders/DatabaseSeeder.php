<?php

namespace Database\Seeders;

use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Lance tous les seeders dans le bon ordre.
     * RoleSeeder doit toujours être en premier.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            // UserSeeder::class,
        ]);
    }
}
