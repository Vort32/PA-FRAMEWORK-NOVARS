<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->phoneNumber(),
            'blood_type' => fake()->randomElement(['A', 'B', 'AB', 'O']) . fake()->randomElement(['+', '-']),
            'allergies' => fake()->sentence(),
            'medical_history' => fake()->paragraph(),
        ];
    }
}
