<?php

namespace Database\Seeders;

use App\Models\AboutOriginSection;
use Illuminate\Database\Seeder;

class AboutOriginSectionSeeder extends Seeder
{
    public function run(): void
    {
        AboutOriginSection::updateOrCreate(
            ['title' => 'Comment tout a commencé'],
            [
                'eyebrow'        => 'Notre naissance',
                'title'          => 'Comment tout a commencé',
                'content'        => "<p>Laravel Côte d'Ivoire est né d'une conviction simple : les développeurs ivoiriens méritent un espace structuré, en français, adapté à leur contexte local. Ce qui a commencé comme un groupe WhatsApp de quelques passionnés de Laravel à Abidjan est rapidement devenu une communauté de plus de 500 développeurs, unie par l'amour du code propre et l'envie de grandir ensemble.</p><p>La communauté a été fondée en 2026 avec une mission claire : créer le hub de référence pour les développeurs Laravel en Côte d'Ivoire et dans la diaspora ivoirienne.</p>",
                'media_type'     => 'none',
                'media_path'     => null,
                'youtube_url'    => null,
                'media_position' => 'right',
                'caption'        => null,
                'is_active'      => true,
            ]
        );
    }
}
