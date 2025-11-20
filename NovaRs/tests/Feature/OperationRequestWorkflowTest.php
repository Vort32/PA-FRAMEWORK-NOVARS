<?php

namespace Tests\Feature;

use App\Enums\OperationRequestStatus;
use App\Enums\OperationStaffStatus;
use App\Enums\OperationStatus;
use App\Enums\UserRole;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Equipment;
use App\Models\Doctor;
use App\Models\Disease;
use App\Models\Operation;
use App\Models\OperationRequest;
use App\Models\Patient;
use App\Models\Room;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OperationRequestWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_submit_operation_request(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $patient = Patient::factory()->create();
        $user = $patient->user;

        $disease = Disease::factory()->create();
        $doctor = Doctor::factory()->create();

        Storage::fake('public');
        $file = UploadedFile::fake()->create('referral.pdf', 120, 'application/pdf');

        $response = $this->actingAs($user)
            ->post(route('patient.operation-requests.store', [], false), [
                'doctor_id' => $doctor->user_id,
                'disease_id' => $disease->id,
                'symptoms_description' => 'Persistent severe abdominal pain requiring surgery recommendation.',
                'preferred_date' => now()->addWeek()->toDateString(),
                'referral_letter' => $file,
            ]);

        $response->assertRedirect(route('patient.dashboard', [], false));

        $request = OperationRequest::where('patient_id', $user->id)->first();

        $this->assertNotNull($request);
        $this->assertEquals(OperationRequestStatus::Pending, $request->status);
        $this->assertEquals($doctor->user_id, $request->doctor_id);
        $this->assertEquals($disease->id, $request->disease_id);

        Storage::disk('public')->assertExists($request->referral_letter_path);

        $downloadResponse = $this->actingAs($user)
            ->get(route('operation-requests.referral-letter', [$request], false));

        $downloadResponse->assertOk();
    }

    public function test_admin_can_approve_operation_request_and_schedule_operation(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $patient = Patient::factory()->create();
        $doctor = Doctor::factory()->create();
        $request = OperationRequest::create([
            'patient_id' => $patient->user->id,
            'doctor_id' => $doctor->user_id,
            'disease_id' => Disease::factory()->create()->id,
            'symptoms_description' => 'Requires immediate surgical attention.',
            'preferred_date' => now()->addDays(3)->toDateString(),
            'status' => OperationRequestStatus::Pending,
        ]);

        $doctorResponse = $this->actingAs($doctor->user)
            ->post(route('doctor.operation-requests.approve', [$request], false), [
                'doctor_notes' => 'Approved for scheduling.',
            ]);

        $doctorResponse->assertRedirect(route('doctor.operation-requests.show', [$request], false));

        $request->refresh();
        $this->assertEquals(OperationRequestStatus::Approved, $request->status);
        $this->assertNotNull($request->approved_at);
        $this->assertEquals('Approved for scheduling.', $request->doctor_notes);

        $admin = User::factory()->create(['role' => UserRole::Admin->value]);
        $room = Room::factory()->create();

        $payload = [
            'scheduled_at' => now()->addDays(2)->setTime(10, 0)->format('Y-m-d\TH:i'),
            'room_id' => $room->id,
            'estimated_duration_minutes' => 90,
            'notes' => 'Schedule per surgeon availability.',
            'admin_notes' => 'Approved and scheduled.',
        ];

        $response = $this->actingAs($admin)
            ->post(route('admin.operation-requests.approve', [$request], false), $payload);

        $response->assertRedirect(route('admin.operation-requests.index', [], false));

        $request->refresh();

        $this->assertEquals(OperationRequestStatus::Approved, $request->status);
        $this->assertNotNull($request->operation_id);

        $this->assertDatabaseHas('operations', [
            'id' => $request->operation_id,
            'patient_id' => $patient->user->id,
            'room_id' => $room->id,
            'status' => OperationStatus::PendingAssignment->value,
        ]);
    }

    public function test_doctor_can_request_operation_with_equipment_and_staff(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $doctor = Doctor::factory()->create();

        $operation = Operation::factory()->create([
            'doctor_id' => null,
            'requested_doctor_id' => null,
            'status' => OperationStatus::PendingAssignment->value,
        ]);

        $staffMembers = Staff::factory()->count(2)->create();
        $equipments = Equipment::factory()->count(2)->create();

        $payload = [
            'staff_ids' => $staffMembers->pluck('id')->toArray(),
            'equipment_ids' => [$equipments[0]->id],
            'equipment_quantities' => [
                $equipments[0]->id => 3,
            ],
            'equipment_notes' => [
                $equipments[0]->id => 'Requires calibration before use.',
            ],
        ];

        $response = $this->actingAs($doctor->user)
            ->post(route('doctor.operations.request.submit', [$operation], false), $payload);

        $response->assertRedirect(route('doctor.operations', [], false));

        $operation->refresh();

        $this->assertEquals($doctor->user_id, $operation->requested_doctor_id);
        $this->assertEquals(OperationStatus::PendingApproval, $operation->status);

        $staffMembers->each(function ($staff) use ($operation) {
            $this->assertDatabaseHas('operation_staff', [
                'operation_id' => $operation->id,
                'staff_id' => $staff->id,
                'status' => OperationStaffStatus::Pending->value,
            ]);
        });

        $this->assertDatabaseHas('operation_equipments', [
            'operation_id' => $operation->id,
            'equipment_id' => $equipments[0]->id,
            'quantity' => 3,
            'notes' => 'Requires calibration before use.',
        ]);
    }
}
