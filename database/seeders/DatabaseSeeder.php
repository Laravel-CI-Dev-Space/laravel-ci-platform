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
            RoleSeeder::class,
            UserSeeder::class,
        ]);
    }
}
