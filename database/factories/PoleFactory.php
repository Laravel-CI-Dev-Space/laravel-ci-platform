<?php

namespace Database\Factories;

use App\Models\Pole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pole>
 */
class PoleFactory extends Factory
{
    protected $model = Pole::class;

    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'name'      => $name,
            'slug'      => \Illuminate\Support\Str::slug($name),
            'position'  => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }

    public function withSlug(string $slug): static
    {
        return $this->state(['slug' => $slug]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
