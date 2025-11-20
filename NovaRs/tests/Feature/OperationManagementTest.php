<?php

namespace Tests\Feature;

use App\Enums\OperationStatus;
use App\Enums\UserRole;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Disease;
use App\Models\Doctor;
use App\Models\Operation;
use App\Models\Patient;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OperationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_schedule_operation(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $admin = User::factory()->create([
            'role' => UserRole::Admin->value,
            'password' => Hash::make('password'),
        ]);

        $patient = Patient::factory()->create();
        $doctor = Doctor::factory()->create();
        $room = Room::factory()->create();
        $disease = Disease::factory()->create();

        $response = $this->actingAs($admin)
            ->post(route('admin.operations.store'), [
                'patient_id' => $patient->user_id,
                'doctor_id' => $doctor->user_id,
                'room_id' => $room->id,
                'disease_id' => $disease->id,
                'scheduled_at' => now()->addDay()->format('Y-m-d H:i'),
                'status' => OperationStatus::Scheduled->value,
                'estimated_duration_minutes' => 90,
                'notes' => 'Test operation notes',
            ]);

        $response->assertRedirect(route('admin.operations.index'));

        $this->assertDatabaseHas('operations', [
            'patient_id' => $patient->user_id,
            'doctor_id' => $doctor->user_id,
            'room_id' => $room->id,
            'status' => OperationStatus::Scheduled->value,
        ]);
    }
}
