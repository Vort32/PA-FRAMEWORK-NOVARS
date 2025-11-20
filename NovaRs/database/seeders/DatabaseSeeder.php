<?php

namespace Database\Seeders;

use App\Enums\OperationOutcomeStatus;
use App\Enums\OperationStatus;
use App\Enums\RoomStatus;
use App\Enums\UserRole;
use App\Models\Disease;
use App\Models\Doctor;
use App\Models\Equipment;
use App\Models\Operation;
use App\Models\OperationReport;
use App\Models\Patient;
use App\Models\Room;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@hospital.test'],
            [
                'name' => 'System Administrator',
                'role' => UserRole::Admin->value,
                'medical_record_number' => null,
                'password' => Hash::make('password'),
            ]
        );

        $doctorData = [
            ['name' => 'Dr. Maya Pratama', 'email' => 'maya.pratama@hospital.test', 'specialization' => 'Cardiology'],
            ['name' => 'Dr. Budi Santoso', 'email' => 'budi.santoso@hospital.test', 'specialization' => 'Orthopedics'],
        ];

        $doctors = collect($doctorData)->values()->map(function (array $data, int $index) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role' => UserRole::Doctor->value,
                    'medical_record_number' => null,
                    'password' => Hash::make('password'),
                ]
            );

            $doctor = Doctor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'specialization' => $data['specialization'],
                    'license_number' => sprintf('LIC-%03d', $index + 1),
                    'years_of_experience' => 8 + ($index * 3),
                    'bio' => 'Specialist in '.$data['specialization'].'.',
                ]
            );

            if (! $doctor->wasRecentlyCreated) {
                $doctor->update(['specialization' => $data['specialization']]);
            }

            return $doctor;
        });

        $staffData = [
            ['name' => 'Sinta Rahma', 'email' => 'sinta.rahma@hospital.test', 'position' => 'Operating Room Nurse'],
            ['name' => 'Rudi Hartono', 'email' => 'rudi.hartono@hospital.test', 'position' => 'Surgical Technician'],
        ];

        $staffMembers = collect($staffData)->values()->map(function (array $data, int $index) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role' => UserRole::Staff->value,
                    'medical_record_number' => null,
                    'password' => Hash::make('password'),
                ]
            );

            $staff = Staff::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'position' => $data['position'],
                    'shift_type' => $index % 2 === 0 ? 'day' : 'night',
                ]
            );

            if (! $staff->wasRecentlyCreated) {
                $staff->update(['position' => $data['position']]);
            }

            return $staff;
        });

        $patients = Patient::factory()->count(5)->create()->each(function (Patient $patient) {
            $patient->user->update([
                'medical_record_number' => fake()->unique()->numerify('MRN-#####'),
                'password' => Hash::make('password'),
            ]);
        });

        $rooms = collect([
            ['name' => 'Operating Room Alpha', 'code' => 'OR-A1'],
            ['name' => 'Operating Room Beta', 'code' => 'OR-B1'],
            ['name' => 'Operating Room Gamma', 'code' => 'OR-C1'],
        ])->map(function (array $data) {
            $room = Room::updateOrCreate(
                ['code' => $data['code']],
                [
                    'name' => $data['name'],
                    'capacity' => 1,
                ]
            );

            if ($room->wasRecentlyCreated && $room->status === null) {
                $room->status = RoomStatus::Available;
                $room->save();
            }

            return $room;
        });

        $equipments = Equipment::factory()->count(8)->create();

        $diseases = Disease::factory()->count(3)->create();

        $rooms->each(function (Room $room) use ($equipments) {
            if ($equipments->isEmpty()) {
                $room->equipments()->detach();
                return;
            }

            $assignments = $equipments->random(min(3, $equipments->count()))->mapWithKeys(function ($equipment) {
                return [$equipment->id => ['quantity' => fake()->numberBetween(1, 4)]];
            })->toArray();

            $room->equipments()->sync($assignments);
        });

        $operations = collect([
            [
                'scheduled_at' => Carbon::now()->setTime(9, 0),
                'status' => OperationStatus::Completed,
                'estimated_duration_minutes' => 120,
            ],
            [
                'scheduled_at' => Carbon::now()->addDays(3)->setTime(11, 0),
                'status' => OperationStatus::Scheduled,
                'estimated_duration_minutes' => 90,
            ],
            [
                'scheduled_at' => Carbon::now()->addWeeks(2)->setTime(14, 0),
                'status' => OperationStatus::Scheduled,
                'estimated_duration_minutes' => 75,
            ],
        ])->map(function (array $data) use ($patients, $doctors, $rooms, $diseases) {
            $patient = $patients->random();
            $doctor = $doctors->random();
            $room = $rooms->random();
            $disease = $diseases->random();

            return Operation::create([
                'patient_id' => $patient->user_id,
                'doctor_id' => $doctor->user_id,
                'room_id' => $room->id,
                'disease_id' => $disease->id,
                'scheduled_at' => $data['scheduled_at'],
                'status' => $data['status'],
                'estimated_duration_minutes' => $data['estimated_duration_minutes'],
                'notes' => fake()->sentence(),
            ]);
        });

        $operations->each(function (Operation $operation) use ($equipments) {
            if ($equipments->isEmpty()) {
                $operation->equipments()->detach();
                return;
            }

            $payload = $equipments->random(min(3, $equipments->count()))->mapWithKeys(fn ($equipment) => [
                $equipment->id => [
                    'quantity' => fake()->numberBetween(1, 2),
                    'notes' => fake()->optional()->sentence(),
                ],
            ])->toArray();

            $operation->equipments()->sync($payload);
        });

        $completedOperation = $operations->firstWhere(fn (Operation $operation) => $operation->status === OperationStatus::Completed);

        if ($completedOperation) {
            OperationReport::create([
                'operation_id' => $completedOperation->id,
                'doctor_id' => $completedOperation->doctor_id,
                'status_outcome' => OperationOutcomeStatus::Success,
                'complications' => null,
                'procedure_details' => 'Procedure completed successfully without complications.',
                'duration_minutes' => $completedOperation->estimated_duration_minutes,
            ]);
        }
    }
}
