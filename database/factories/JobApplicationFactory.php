<?php

namespace Database\Factories;

use App\Enums\Jobs\JobApplicationStatus;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobApplication>
 */
class JobApplicationFactory extends Factory
{
    protected $model = JobApplication::class;

    public function definition(): array
    {
        return [
            'job_offer_id' => JobOffer::factory()->active(),
            'user_id'      => User::factory(),
            'cv_path'      => null,
            'cover_letter' => fake()->optional()->paragraph(),
            'status'       => JobApplicationStatus::PENDING,
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn () => ['status' => JobApplicationStatus::ACCEPTED]);
    }
}
