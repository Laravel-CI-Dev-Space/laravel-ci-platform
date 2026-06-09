<?php

namespace Database\Seeders;

use App\Enums\Events\EventRegistrationStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Sprint Roger — Événements & Job Board (données de démo + membre test).
 */
class SprintRogerSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            EventTypeSeeder::class,
            EventSeeder::class,
            JobSeeder::class,
        ]);

        $member = User::firstOrCreate(
            ['email' => 'roger@laravelci.test'],
            [
                'name'              => 'Roger DA SIE',
                'github_id'         => '900000001',
                'github_username'   => 'roger-da',
                'avatar'            => 'https://avatars.githubusercontent.com/u/1?v=4',
                'is_active'         => true,
                'email_verified_at' => now(),
            ],
        );

        if (! $member->hasRole('member')) {
            $member->assignRole('member');
        }

        Event::query()
            ->where('start_date', '>', now())
            ->orderBy('start_date')
            ->limit(2)
            ->get()
            ->each(function (Event $event) use ($member) {
                EventRegistration::firstOrCreate(
                    ['event_id' => $event->id, 'user_id' => $member->id],
                    ['status' => EventRegistrationStatus::CONFIRMED],
                );
            });

        $this->command?->info('✅ Sprint Roger : events, jobs et membre test prêts.');
    }
}
