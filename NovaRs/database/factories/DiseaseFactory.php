<?php

namespace Database\Factories;

use App\Models\Disease;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Disease>
 */
class DiseaseFactory extends Factory
{
    protected $model = Disease::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Appendicitis', 'Cholelithiasis', 'Hernia']),
            'icd_code' => fake()->unique()->bothify('K##.#'),
            'description' => fake()->paragraph(),
        ];
    }
}
