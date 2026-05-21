<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name'        => $name,
            'description' => fake()->paragraph(),
            'logo'        => fake()->imageUrl(120, 120, 'business'),
            'website'     => 'https://' . fake()->domainName(),
        ];
    }
}
