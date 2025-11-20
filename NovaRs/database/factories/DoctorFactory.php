<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(fn () => [
                'role' => UserRole::Doctor->value,
                'medical_record_number' => null,
            ]),
            'specialization' => fake()->randomElement(['Cardiology', 'Neurology', 'Orthopedics', 'General Surgery']),
            'license_number' => fake()->unique()->numerify('LIC-#####'),
            'years_of_experience' => fake()->numberBetween(3, 25),
            'bio' => fake()->paragraph(),
        ];
    }
}
