<?php

namespace Database\Factories;

use App\Enums\OperationOutcomeStatus;
use App\Enums\UserRole;
use App\Models\Operation;
use App\Models\OperationReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OperationReport>
 */
class OperationReportFactory extends Factory
{
    protected $model = OperationReport::class;

    public function definition(): array
    {
        return [
            'operation_id' => Operation::factory(),
            'doctor_id' => User::factory()->state(fn () => [
                'role' => UserRole::Doctor->value,
                'medical_record_number' => null,
            ]),
            'status_outcome' => OperationOutcomeStatus::Success->value,
            'complications' => fake()->optional()->sentence(),
            'procedure_details' => fake()->paragraph(),
            'duration_minutes' => fake()->numberBetween(45, 240),
        ];
    }
}
