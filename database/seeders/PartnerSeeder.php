<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    public function run(): void
    {
        $partners = [
            ['name' => 'Laravel France',   'icon' => 'fa-solid fa-hippo', 'type' => 'community', 'order' => 1],
            ['name' => 'Laravel Cameroun', 'icon' => 'fa-solid fa-hippo', 'type' => 'community', 'order' => 2],
            ['name' => 'Laravel Sénégal',  'icon' => 'fa-solid fa-hippo', 'type' => 'community', 'order' => 3],
            ['name' => 'Laravel Nigeria',  'icon' => 'fa-solid fa-hippo', 'type' => 'community', 'order' => 4],
        ];

        foreach ($partners as $partner) {
            Partner::updateOrCreate(
                ['name' => $partner['name']],
                array_merge($partner, ['logo' => null, 'url' => null, 'is_active' => true])
            );
        }
    }
}
