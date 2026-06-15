<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\JobContractType;
use App\Enums\JobLevel;
use App\Enums\JobOfferStatus;
use App\Models\Company;
use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<JobOffer>
 */
class JobOfferFactory extends Factory
{
    protected $model = JobOffer::class;

    public function definition(): array
    {
        $title = fake()->jobTitle();

        return [
            'company_id'    => Company::factory(),
            'posted_by'     => User::factory(),
            'title'         => $title,
            'slug'          => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 1000000),
            'description'   => fake()->paragraphs(3, true),
            'contract_type' => JobContractType::Cdi,
            'level'         => JobLevel::Intermediate,
            'location'      => 'Abidjan',
            'country'       => 'Côte d\'Ivoire',
            'status'        => JobOfferStatus::Draft,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'       => JobOfferStatus::Active,
            'published_at' => now(),
            'expires_at'   => now()->addMonth(),
        ]);
    }
}
