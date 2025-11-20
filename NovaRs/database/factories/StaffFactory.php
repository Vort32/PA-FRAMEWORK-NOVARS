<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Staff>
 */
class StaffFactory extends Factory
{
    protected $model = Staff::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(fn () => [
                'role' => UserRole::Staff->value,
                'medical_record_number' => null,
            ]),
            'position' => fake()->randomElement(['Operating Room Nurse', 'Anesthesiology Assistant', 'Circulating Nurse']),
            'shift_type' => fake()->randomElement(['day', 'night', 'swing']),
        ];
    }
}
