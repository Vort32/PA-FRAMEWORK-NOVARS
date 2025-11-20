<?php

namespace App\Imports;

use App\Enums\UserRole;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PatientImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row): Patient
    {
        $data = $this->normalizeRow($row);

        $user = User::updateOrCreate(
            ['email' => $data['email']],
            array_filter([
                'name' => $data['name'],
                'role' => UserRole::Patient,
                'phone' => $data['phone'] ?? null,
                'gender' => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'address' => $data['address'] ?? null,
                'medical_record_number' => $data['medical_record_number'] ?? $this->generateMrn(),
                'password' => Hash::make($data['password'] ?? 'password'),
            ], static fn ($value) => $value !== null)
        );

        return Patient::updateOrCreate(
            ['user_id' => $user->id],
            [
                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                'blood_type' => $data['blood_type'] ?? null,
                'allergies' => $data['allergies'] ?? null,
                'medical_history' => $data['medical_history'] ?? null,
            ]
        );
    }

    public function rules(): array
    {
        return [
            '*.name' => ['required', 'string', 'max:255'],
            '*.email' => ['required', 'email'],
            '*.phone' => ['nullable', 'string', 'max:30'],
            '*.gender' => ['nullable', 'string', 'max:20'],
            '*.date_of_birth' => ['nullable', 'date'],
            '*.address' => ['nullable', 'string'],
            '*.medical_record_number' => ['nullable', 'string', 'max:255'],
            '*.emergency_contact_name' => ['nullable', 'string', 'max:255'],
            '*.emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            '*.blood_type' => ['nullable', 'string', 'max:10'],
            '*.allergies' => ['nullable', 'string'],
            '*.medical_history' => ['nullable', 'string'],
        ];
    }

    protected function normalizeRow(array $row): array
    {
        $row = array_change_key_case($row, CASE_LOWER);

        $defaults = [
            'name' => null,
            'email' => null,
            'phone' => null,
            'gender' => null,
            'date_of_birth' => null,
            'address' => null,
            'medical_record_number' => null,
            'emergency_contact_name' => null,
            'emergency_contact_phone' => null,
            'blood_type' => null,
            'allergies' => null,
            'medical_history' => null,
            'password' => null,
        ];

        return Arr::only(array_merge($defaults, $row), array_keys($defaults));
    }

    protected function generateMrn(): string
    {
        return 'MRN-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
    }
}
