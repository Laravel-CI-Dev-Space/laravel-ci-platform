<?php

namespace Database\Seeders;

use App\Enums\Jobs\JobOfferStatus;
use App\Enums\Jobs\JobOfferType;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\JobOffer;
use App\Models\JobSkill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            ['name' => 'Développement backend', 'slug' => 'backend'],
            ['name' => 'Développement frontend', 'slug' => 'frontend'],
            ['name' => 'DevOps', 'slug' => 'devops'],
        ])->map(fn (array $data) => JobCategory::firstOrCreate(['slug' => $data['slug']], $data));

        $skills = collect([
            'Laravel', 'PHP', 'Filament', 'Livewire', 'Vue.js',
            'Tailwind CSS', 'MySQL', 'Docker', 'Git', 'Pest',
        ])->map(fn (string $name) => JobSkill::firstOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name],
        ));

        $companies = Company::factory()->count(3)->create();

        $offers = [
            [
                'title'    => 'Développeur Laravel Senior',
                'type'     => JobOfferType::CDI,
                'location' => 'Abidjan',
                'status'   => JobOfferStatus::ACTIVE,
            ],
            [
                'title'    => 'Ingénieur Full Stack Laravel / Vue',
                'type'     => JobOfferType::REMOTE,
                'location' => 'Remote',
                'status'   => JobOfferStatus::ACTIVE,
            ],
            [
                'title'    => 'Freelance Filament — 6 mois',
                'type'     => JobOfferType::FREELANCE,
                'location' => 'Remote',
                'status'   => JobOfferStatus::ACTIVE,
            ],
            [
                'title'    => 'Stage développeur PHP',
                'type'     => JobOfferType::STAGE,
                'location' => 'Bouaké',
                'status'   => JobOfferStatus::ACTIVE,
            ],
            [
                'title'    => 'Lead Developer Laravel (expirée)',
                'type'     => JobOfferType::CDI,
                'location' => 'Abidjan',
                'status'   => JobOfferStatus::EXPIRED,
            ],
            [
                'title'    => 'Offre en attente de validation',
                'type'     => JobOfferType::CDI,
                'location' => 'Abidjan',
                'status'   => JobOfferStatus::DRAFT,
            ],
        ];

        foreach ($offers as $index => $data) {
            $status = $data['status'];
            unset($data['status']);

            $factory = JobOffer::factory()
                ->for($companies[$index % $companies->count()])
                ->for($categories[$index % $categories->count()], 'category');

            $offer = match ($status) {
                JobOfferStatus::ACTIVE  => $factory->active()->create($data),
                JobOfferStatus::EXPIRED => $factory->expired()->create($data),
                default                 => $factory->draft()->create($data),
            };

            $offer->skills()->attach(
                $skills->random(min(4, $skills->count()))->pluck('id'),
            );
        }

        JobOffer::factory()
            ->count(4)
            ->active()
            ->create()
            ->each(function (JobOffer $offer) use ($skills) {
                $offer->skills()->attach(
                    $skills->random(min(4, $skills->count()))->pluck('id'),
                );
            });

        $this->command?->info('✅ Job board de démo créé.');
    }
}
