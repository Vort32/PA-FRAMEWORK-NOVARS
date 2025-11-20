<?php

namespace Database\Factories;

use App\Enums\OperationStatus;
use App\Enums\UserRole;
use App\Models\Disease;
use App\Models\Operation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Operation>
 */
class OperationFactory extends Factory
{
    protected $model = Operation::class;

    public function definition(): array
    {
        return [
            'patient_id' => User::factory()->state(fn () => [
                'role' => UserRole::Patient->value,
            ]),
            'doctor_id' => User::factory()->state(fn () => [
                'role' => UserRole::Doctor->value,
                'medical_record_number' => null,
            ]),
            'room_id' => Room::factory(),
            'disease_id' => Disease::factory(),
            'scheduled_at' => fake()->dateTimeBetween('+1 day', '+1 month'),
            'status' => OperationStatus::Scheduled->value,
            'estimated_duration_minutes' => fake()->numberBetween(60, 180),
            'notes' => fake()->sentence(),
        ];
    }
}
