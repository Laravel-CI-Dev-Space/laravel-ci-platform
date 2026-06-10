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
            'avatar'            => null,
            'github_id'         => (string) fake()->unique()->numberBetween(1000000, 99999999),
            'github_username'   => fake()->unique()->userName(),
            'is_active'         => true,
            'email_verified_at' => now(),
            'remember_token'    => Str::random(10),
        ];
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

    /**
     * Active member with the "member" role assigned.
     */
    public function membreActif(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active'         => true,
            'email_verified_at' => now(),
        ])->afterCreating(function (User $user) {
            $user->assignRole('member');
        });
    }
}
