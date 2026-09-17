<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PartnerSeeder extends Seeder
{
    private function syncToDisk(string $path): void
    {
        $local = public_path('assets/' . $path);
        if (file_exists($local) && ! Storage::disk('assets')->exists($path)) {
            Storage::disk('assets')->put($path, file_get_contents($local));
        }
    }

    public function run(): void
    {
        $partners = [
            [
                'name'  => 'Laravel France',
                'logo'  => 'communaute_part/laravel_france_logo.jpeg',
                'url'   => 'https://laravel.fr',
                'type'  => 'community',
                'order' => 1,
            ],
            [
                'name'  => 'Laravel Cameroun',
                'logo'  => 'communaute_part/laravel_cameroun_logo.jpeg',
                'url'   => null,
                'type'  => 'community',
                'order' => 2,
            ],
            [
                'name'  => 'Laravel Sénégal',
                'logo'  => 'communaute_part/laravel_senegal_logo.jpeg',
                'url'   => null,
                'type'  => 'community',
                'order' => 3,
            ],
            [
                'name'  => 'Laravel Denmark',
                'logo'  => 'communaute_part/laraveldenmark_logo.jpeg',
                'url'   => null,
                'type'  => 'community',
                'order' => 4,
            ],
            [
                'name'  => 'Laravel Live UK London',
                'logo'  => 'communaute_part/laravel_live_uk_london_logo.jpeg',
                'url'   => null,
                'type'  => 'community',
                'order' => 5,
            ],
        ];

        foreach ($partners as $partner) {
            $this->syncToDisk($partner['logo']);

            Partner::updateOrCreate(
                ['name' => $partner['name']],
                array_merge($partner, ['icon' => null, 'is_active' => true])
            );
        }
    }
}
