<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Exports\PatientsExport;
use App\Imports\PatientImport;
use App\Models\Operation;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class PatientController extends Controller
{
    public function dashboard(): View
    {
        Gate::authorize('access-patient');

        $user = auth()->user();
        $operations = $user->patientOperations()
            ->with(['doctor', 'room', 'report'])
            ->orderByDesc('scheduled_at')
            ->get();

        $operationRequests = $user->operationRequests()
            ->with(['disease', 'operation.room', 'doctor'])
            ->orderByDesc('created_at')
            ->get();

        return view('patient.dashboard', compact('operations', 'operationRequests'));
    }

    public function index(): View
    {
        Gate::authorize('access-admin');

        $patients = Patient::with('user')->paginate(10);

        return view('admin.patients.index', compact('patients'));
    }

    public function create(): View
    {
        Gate::authorize('access-admin');

        return view('admin.patients.create');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        Gate::authorize('access-admin');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:30'],
            'gender' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'allergies' => ['nullable', 'string'],
            'medical_history' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'] ?? 'password'),
            'role' => UserRole::Patient,
            'phone' => $data['phone'] ?? null,
            'gender' => $data['gender'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'address' => $data['address'] ?? null,
            'medical_record_number' => Str::upper('MRN-'.now()->format('ymd').'-'.Str::random(4)),
        ]);

        $patient = Patient::create([
            'user_id' => $user->id,
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
            'blood_type' => $data['blood_type'] ?? null,
            'allergies' => $data['allergies'] ?? null,
            'medical_history' => $data['medical_history'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Patient created',
                'patient' => $patient->load('user'),
            ], 201);
        }

        return redirect()->route('admin.patients.index')->with('status', 'Patient created');
    }

    public function edit(Patient $patient): View
    {
        Gate::authorize('access-admin');

        $patient->load('user');

        return view('admin.patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient): RedirectResponse|JsonResponse
    {
        Gate::authorize('access-admin');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$patient->user_id],
            'password' => ['nullable', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:30'],
            'gender' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'allergies' => ['nullable', 'string'],
            'medical_history' => ['nullable', 'string'],
        ]);

        $patient->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'gender' => $data['gender'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'address' => $data['address'] ?? null,
            'password' => empty($data['password']) ? $patient->user->password : Hash::make($data['password']),
        ]);

        $patient->update([
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
            'blood_type' => $data['blood_type'] ?? null,
            'allergies' => $data['allergies'] ?? null,
            'medical_history' => $data['medical_history'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Patient updated',
                'patient' => $patient->load('user'),
            ]);
        }

        return redirect()->route('admin.patients.index')->with('status', 'Patient updated');
    }

    public function destroy(Patient $patient): RedirectResponse|JsonResponse
    {
        Gate::authorize('access-admin');

        $patient->user()->delete();
        $patient->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Patient deleted']);
        }

        return redirect()->route('admin.patients.index')->with('status', 'Patient deleted');
    }

    public function export(Request $request)
    {
        Gate::authorize('access-admin');

        $format = $request->query('format', 'xlsx');
        $export = new PatientsExport();

        if ($format === 'pdf') {
            return $export->downloadPdf('patients.pdf');
        }

        return Excel::download($export, 'patients.xlsx');
    }

    public function import(Request $request): RedirectResponse|JsonResponse
    {
        Gate::authorize('access-admin');

        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,csv', 'max:5120'],
        ]);

        Excel::import(new PatientImport(), $data['file']);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Patients imported']);
        }

        return redirect()->route('admin.patients.index')->with('status', 'Patients imported successfully');
    }

    public function operations(): View
    {
        Gate::authorize('access-patient');

        $operations = auth()->user()
            ->patientOperations()
            ->with(['doctor', 'room', 'report'])
            ->orderByDesc('scheduled_at')
            ->paginate(10);

        return view('patient.my-operations.index', compact('operations'));
    }

    public function report(Operation $operation): View
    {
        Gate::authorize('access-patient');

        abort_unless($operation->patient_id === auth()->id(), 403);

        $operation->load(['doctor', 'room', 'report.doctor']);

        return view('patient.reports.show', compact('operation'));
    }
}
