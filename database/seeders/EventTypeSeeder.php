<?php

namespace Database\Seeders;

use App\Models\EventType;
use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Meetup', 'slug' => 'meetup'],
            ['name' => 'Webinar', 'slug' => 'webinar'],
            ['name' => 'Hackathon', 'slug' => 'hackathon'],
        ];

        foreach ($types as $type) {
            EventType::firstOrCreate(['slug' => $type['slug']], $type);
        }

        $this->command?->info('✅ Types d\'événements créés.');
    }
}
