<?php

namespace Database\Seeders;

use App\Enums\Jobs\JobOfferStatus;
use App\Enums\Jobs\JobOfferType;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\JobOffer;
use App\Models\JobSkill;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class JobSeeder extends Seeder
{
    /** @var Collection<int, JobSkill> */
    private Collection $skills;

    public function run(): void
    {
        $categories   = $this->seedCategories();
        $this->skills = $this->resolveSkills();
        $companies    = Company::factory()->count(12)->create();

        $this->seedCuratedOffers($companies, $categories);
        $this->seedBulkOffers($companies, $categories);

        $total = JobOffer::count();
        $this->command?->info("✅ Job board de démo créé ({$total} offres au total).");
    }

    /** @return Collection<int, JobCategory> */
    private function seedCategories(): Collection
    {
        $names = [
            ['name' => 'Développement backend', 'slug' => 'backend'],
            ['name' => 'Développement frontend', 'slug' => 'frontend'],
            ['name' => 'Full stack', 'slug' => 'full-stack'],
            ['name' => 'DevOps', 'slug' => 'devops'],
            ['name' => 'Mobile', 'slug' => 'mobile'],
            ['name' => 'QA & tests', 'slug' => 'qa'],
        ];

        return collect($names)->map(
            fn (array $data) => JobCategory::firstOrCreate(['slug' => $data['slug']], $data),
        );
    }

    /** @return Collection<int, JobSkill> */
    private function resolveSkills(): Collection
    {
        if (JobSkill::query()->exists()) {
            return JobSkill::all();
        }

        $names = [
            'Laravel', 'PHP', 'Filament', 'Livewire', 'Vue.js',
            'Tailwind CSS', 'MySQL', 'Docker', 'Git', 'Pest',
            'React', 'TypeScript', 'Redis', 'AWS',
        ];

        return collect($names)->map(
            fn (string $name) => JobSkill::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            ),
        );
    }

    /**
     * @param  Collection<int, Company>  $companies
     * @param  Collection<int, JobCategory>  $categories
     */
    private function seedCuratedOffers(Collection $companies, Collection $categories): void
    {
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

            $this->attachRandomSkills($offer);
        }
    }

    /**
     * @param  Collection<int, Company>  $companies
     * @param  Collection<int, JobCategory>  $categories
     */
    private function seedBulkOffers(Collection $companies, Collection $categories): void
    {
        $this->createOffersWithRotation($companies, $categories, 24, fn () => JobOffer::factory()->active());
        $this->createOffersWithRotation($companies, $categories, 6, fn () => JobOffer::factory()->remote()->active());
        $this->createOffersWithRotation($companies, $categories, 8, fn () => JobOffer::factory()->expired());
        $this->createOffersWithRotation($companies, $categories, 6, fn () => JobOffer::factory()->draft());
    }

    /**
     * @param  Collection<int, Company>  $companies
     * @param  Collection<int, JobCategory>  $categories
     * @param  callable(): Factory<JobOffer>  $factory
     */
    private function createOffersWithRotation(
        Collection $companies,
        Collection $categories,
        int $count,
        callable $factory,
    ): void {
        $factory()
            ->count($count)
            ->sequence(fn ($sequence) => [
                'company_id'  => $companies[$sequence->index % $companies->count()]->id,
                'category_id' => $categories[$sequence->index % $categories->count()]->id,
            ])
            ->create()
            ->each(fn (JobOffer $offer) => $this->attachRandomSkills($offer));
    }

    private function attachRandomSkills(JobOffer $offer): void
    {
        if ($this->skills->isEmpty()) {
            return;
        }

        $count = min(fake()->numberBetween(2, 6), $this->skills->count());

        $offer->skills()->syncWithoutDetaching(
            $this->skills->random($count)->pluck('id'),
        );
    }
}
