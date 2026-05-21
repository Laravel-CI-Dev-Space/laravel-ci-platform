<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'avatar'            => fake()->imageUrl(200, 200, 'people'),
            'github_id'         => (string) fake()->unique()->numerify('########'),
            'github_username'   => fake()->unique()->userName(),
            'is_active'         => true,
            'email_verified_at' => now(),
            'remember_token'    => Str::random(10),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function membreActif(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('membre-actif');
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
