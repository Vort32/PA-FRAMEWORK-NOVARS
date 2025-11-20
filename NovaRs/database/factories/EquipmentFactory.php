<?php

namespace Database\Factories;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Equipment>
 */
class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Scalpel Set', 'Anesthesia Machine', 'Surgical Light', 'Electrocautery Unit']),
            'category' => fake()->randomElement(['Surgical', 'Monitoring', 'Support']),
            'serial_number' => fake()->unique()->bothify('EQ-#####'),
            'quantity_available' => fake()->numberBetween(1, 10),
            'description' => fake()->sentence(),
        ];
    }
}
