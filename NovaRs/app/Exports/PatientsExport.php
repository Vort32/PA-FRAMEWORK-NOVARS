<?php

namespace App\Exports;

use App\Models\Patient;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PatientsExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return Patient::with('user')->orderByDesc('created_at')->get()->map(function (Patient $patient) {
            $user = $patient->user;

            return [
                'medical_record_number' => $user->medical_record_number,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'gender' => $user->gender,
                'date_of_birth' => optional($user->date_of_birth)->format('Y-m-d'),
                'address' => $user->address,
                'emergency_contact_name' => $patient->emergency_contact_name,
                'emergency_contact_phone' => $patient->emergency_contact_phone,
                'blood_type' => $patient->blood_type,
                'allergies' => $patient->allergies,
                'medical_history' => $patient->medical_history,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Medical Record Number',
            'Name',
            'Email',
            'Phone',
            'Gender',
            'Date of Birth',
            'Address',
            'Emergency Contact Name',
            'Emergency Contact Phone',
            'Blood Type',
            'Allergies',
            'Medical History',
        ];
    }

    public function downloadPdf(string $fileName)
    {
        $patients = $this->collection();

        return Pdf::loadView('admin.patients.export-pdf', [
            'patients' => $patients,
            'headings' => $this->headings(),
        ])->setPaper('a4', 'landscape')->download($fileName);
    }
}
