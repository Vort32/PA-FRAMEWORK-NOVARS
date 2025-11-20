<?php

namespace Tests\Feature;

use App\Enums\OperationOutcomeStatus;
use App\Enums\OperationStatus;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Doctor;
use App\Models\Operation;
use App\Models\Patient;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_submit_operation_report(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();
        $room = Room::factory()->create();

        $operation = Operation::create([
            'patient_id' => $patient->user_id,
            'doctor_id' => $doctor->user_id,
            'room_id' => $room->id,
            'scheduled_at' => now()->subHour(),
            'status' => OperationStatus::Ongoing->value,
            'estimated_duration_minutes' => 120,
        ]);

        $response = $this->actingAs($doctor->user)
            ->post(route('doctor.reports.store', $operation), [
                'status_outcome' => OperationOutcomeStatus::Success->value,
                'complications' => null,
                'procedure_details' => 'Procedure completed successfully.',
                'duration_minutes' => 110,
            ]);

        $response->assertRedirect(route('doctor.dashboard'));

        $this->assertDatabaseHas('operation_reports', [
            'operation_id' => $operation->id,
            'doctor_id' => $doctor->user_id,
            'status_outcome' => OperationOutcomeStatus::Success->value,
        ]);

        $this->assertDatabaseHas('operations', [
            'id' => $operation->id,
            'status' => OperationStatus::Completed->value,
        ]);
    }
}
