<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $users = User::factory(5)->create();
        }

        // Questions épinglées — toujours visibles en tête de liste
        Question::factory(2)
            ->pinned()
            ->sequence(fn ($s) => ['user_id' => $users->random()->id])
            ->create();

        // Questions populaires
        Question::factory(5)
            ->popular()
            ->sequence(fn ($s) => ['user_id' => $users->random()->id])
            ->create();

        // Questions fermées
        Question::factory(3)
            ->closed()
            ->sequence(fn ($s) => ['user_id' => $users->random()->id])
            ->create();

        // Questions normales
        Question::factory(10)
            ->sequence(fn ($s) => ['user_id' => $users->random()->id])
            ->create();
    }
}
