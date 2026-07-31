<?php

namespace Database\Factories;

use App\Models\Pole;
use App\Models\PoleMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PoleMember>
 */
class PoleMemberFactory extends Factory
{
    protected $model = PoleMember::class;

    public function definition(): array
    {
        return [
            'pole_id'    => Pole::factory(),
            'user_id'    => null,
            'first_name' => fake()->firstName(),
            'last_name'  => fake()->lastName(),
            'email'      => fake()->unique()->safeEmail(),
            'poste'      => fake()->jobTitle(),
            'role'       => 'membre',
            'status'     => 'actif',
            'order'      => fake()->numberBetween(1, 10),
        ];
    }

    public function actif(): static
    {
        return $this->state(['status' => 'actif']);
    }

    public function inactif(): static
    {
        return $this->state(['status' => 'inactif']);
    }

    public function forUser(User $user): static
    {
        return $this->state(['user_id' => $user->id]);
    }

    public function forPole(Pole $pole): static
    {
        return $this->state(['pole_id' => $pole->id]);
    }
}
