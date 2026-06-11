<?php

namespace Database\Factories;

use App\Enums\Jobs\JobOfferType;
use App\Models\JobAlert;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<JobAlert> */
class JobAlertFactory extends Factory
{
    protected $model = JobAlert::class;

    public function definition(): array
    {
        return [
            'user_id'   => User::factory(),
            'keywords'  => fake()->optional()->randomElement(['laravel', 'php symfony', 'devops']),
            'location'  => null,
            'type'      => null,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function withKeywords(string $keywords): static
    {
        return $this->state(fn () => ['keywords' => $keywords]);
    }

    public function withLocation(string $location): static
    {
        return $this->state(fn () => ['location' => $location]);
    }

    public function withType(JobOfferType $type): static
    {
        return $this->state(fn () => ['type' => $type]);
    }
}
