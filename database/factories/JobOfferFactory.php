<?php

namespace Database\Factories;

use App\Enums\Jobs\JobOfferStatus;
use App\Enums\Jobs\JobOfferType;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\JobOffer;
use App\Models\JobSkill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobOffer>
 */
class JobOfferFactory extends Factory
{
    protected $model = JobOffer::class;

    public function definition(): array
    {
        return [
            'company_id'  => Company::factory(),
            'category_id' => JobCategory::factory(),
            'title'       => fake()->jobTitle(),
            'description' => fake()->paragraphs(4, true),
            'location'    => fake()->randomElement(['Abidjan', 'Bouaké', 'Remote', 'Yamoussoukro']),
            'type'        => fake()->randomElement(JobOfferType::cases()),
            'salary'      => fake()->optional(0.7)->randomElement(['500 000 - 800 000 FCFA', '800 000 - 1 200 000 FCFA', 'Sur devis']),
            'deadline'    => fake()->dateTimeBetween('+2 weeks', '+3 months')->format('Y-m-d'),
            'status'      => JobOfferStatus::DRAFT,
            'created_at'  => now(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status'     => JobOfferStatus::ACTIVE,
            'created_at' => now()->subDays(fake()->numberBetween(1, 20)),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => JobOfferStatus::DRAFT]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status'     => JobOfferStatus::EXPIRED,
            'created_at' => now()->subDays(35),
            'deadline'   => now()->subDays(5)->toDateString(),
        ]);
    }

    public function remote(): static
    {
        return $this->state(fn () => [
            'type'     => JobOfferType::REMOTE,
            'location' => 'Remote',
        ]);
    }

    public function withSkills(int $count = 3): static
    {
        return $this->afterCreating(function (JobOffer $offer) use ($count) {
            $skills = JobSkill::factory()->count($count)->create();
            $offer->skills()->attach($skills);
        });
    }
}
