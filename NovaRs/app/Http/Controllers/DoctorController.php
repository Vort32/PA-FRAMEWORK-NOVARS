<?php

namespace App\Http\Controllers;

use App\Enums\OperationStaffStatus;
use App\Enums\OperationStatus;
use App\Enums\OperationRequestStatus;
use App\Enums\UserRole;
use App\Models\Doctor;
use App\Models\Equipment;
use App\Models\Operation;
use App\Models\OperationRequest;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{
    public function dashboard(): View
    {
        Gate::authorize('access-doctor');

        $user = auth()->user();

        $todayOperations = Operation::query()
            ->where('doctor_id', $user->id)
            ->whereDate('scheduled_at', Carbon::today())
            ->with(['patient', 'room'])
            ->orderBy('scheduled_at')
            ->get();

        $operationSummary = Operation::query()
            ->selectRaw('status, COUNT(*) as total')
            ->where('doctor_id', $user->id)
            ->groupBy('status')
            ->pluck('total', 'status');

        $recentReports = $user->operationReports()->latest()->limit(5)->with('operation.patient')->get();

        $referralRequests = OperationRequest::query()
            ->where('doctor_id', $user->id)
            ->whereIn('status', [OperationRequestStatus::Pending->value, OperationRequestStatus::Approved->value])
            ->with(['patient', 'disease'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('doctor.dashboard', compact('todayOperations', 'operationSummary', 'recentReports', 'referralRequests'));
    }

    public function operations(Request $request): View
    {
        Gate::authorize('access-doctor');

        $user = auth()->user();

        $assignedOperations = Operation::query()
            ->where('doctor_id', $user->id)
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->with(['patient', 'room'])
            ->orderByDesc('scheduled_at')
            ->paginate(10)
            ->appends($request->query());

        $pendingRequests = Operation::query()
            ->where('requested_doctor_id', $user->id)
            ->with(['patient', 'room', 'staffMembers.user'])
            ->orderBy('scheduled_at')
            ->get();

        $availableOperations = Operation::query()
            ->whereNull('doctor_id')
            ->whereNull('requested_doctor_id')
            ->where('status', OperationStatus::PendingAssignment)
            ->with(['patient', 'room'])
            ->orderBy('scheduled_at')
            ->paginate(10, ['*'], 'available_page')
            ->appends($request->query());

        return view('doctor.operations.index', [
            'assignedOperations' => $assignedOperations,
            'pendingRequests' => $pendingRequests,
            'availableOperations' => $availableOperations,
            'statuses' => OperationStatus::cases(),
        ]);
    }

    public function showRequestForm(Operation $operation): View
    {
        Gate::authorize('access-doctor');

        $this->ensureOperationAvailableForRequest($operation);

        $operation->load(['patient', 'room', 'equipments', 'staffMembers' => fn ($query) => $query->wherePivot('status', OperationStaffStatus::Pending->value)]);

        $staffMembers = Staff::with('user')->get()->sortBy(fn ($staff) => $staff->user?->name)->values();
        $selectedStaffIds = $operation->staffMembers->pluck('id')->all();

        $equipments = Equipment::orderBy('name')->get();

        return view('doctor.operations.request', compact('operation', 'staffMembers', 'selectedStaffIds', 'equipments'));
    }

    public function submitRequest(Request $request, Operation $operation): RedirectResponse
    {
        Gate::authorize('access-doctor');

        $this->ensureOperationAvailableForRequest($operation);

        $data = $request->validate([
            'staff_ids' => ['nullable', 'array'],
            'staff_ids.*' => ['integer', 'exists:staff,id'],
            'equipment_ids' => ['nullable', 'array'],
            'equipment_ids.*' => ['integer', 'exists:equipments,id'],
            'equipment_quantities' => ['nullable', 'array'],
            'equipment_quantities.*' => ['integer', 'min:1'],
            'equipment_notes' => ['nullable', 'array'],
            'equipment_notes.*' => ['nullable', 'string', 'max:255'],
        ]);

        $staffIds = collect($data['staff_ids'] ?? [])->unique()->values();
        $equipmentIds = collect($data['equipment_ids'] ?? [])->unique()->values();
        $equipmentQuantities = collect($request->input('equipment_quantities', []));
        $equipmentNotes = collect($request->input('equipment_notes', []));

        DB::transaction(function () use ($operation, $staffIds, $equipmentIds, $equipmentQuantities, $equipmentNotes) {
            $operation->requested_doctor_id = auth()->id();
            $operation->status = OperationStatus::PendingApproval;
            $operation->save();

            $operation->staffMembers()
                ->wherePivot('status', OperationStaffStatus::Pending->value)
                ->detach();

            if ($staffIds->isNotEmpty()) {
                $payload = $staffIds->mapWithKeys(fn ($id) => [
                    $id => ['status' => OperationStaffStatus::Pending->value],
                ])->toArray();

                $operation->staffMembers()->syncWithoutDetaching($payload);
            }

            $equipmentPayload = $equipmentIds->mapWithKeys(function ($id) use ($equipmentQuantities, $equipmentNotes) {
                $quantity = max(1, (int) $equipmentQuantities->get($id, 1));

                return [
                    $id => [
                        'quantity' => $quantity,
                        'notes' => $equipmentNotes->get($id),
                    ],
                ];
            })->toArray();

            $operation->equipments()->sync($equipmentPayload);
        });

        return redirect()->route('doctor.operations')->with('status', 'Operation request submitted for approval.');
    }

    protected function ensureOperationAvailableForRequest(Operation $operation): void
    {
        $operation->refresh();

        abort_if($operation->doctor_id !== null, 403, 'Operation already assigned to a doctor.');
        abort_if(
            $operation->requested_doctor_id !== null && $operation->requested_doctor_id !== auth()->id(),
            403,
            'Operation already requested by another doctor.'
        );
        abort_if($operation->status !== OperationStatus::PendingAssignment && $operation->status !== OperationStatus::PendingApproval,
            403,
            'Operation is not open for requests.');
    }

    public function reports(): View
    {
        Gate::authorize('access-doctor');

        $reports = auth()->user()
            ->operationReports()
            ->with('operation.patient')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('doctor.reports.index', compact('reports'));
    }

    public function index(): View
    {
        Gate::authorize('access-admin');

        $doctors = Doctor::with('user')->paginate(10);

        return view('admin.doctors.index', compact('doctors'));
    }

    public function create(): View
    {
        Gate::authorize('access-admin');

        return view('admin.doctors.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('access-admin');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:8'],
            'specialization' => ['required', 'string', 'max:255'],
            'license_number' => ['required', 'string', 'max:255', 'unique:doctors,license_number'],
            'years_of_experience' => ['nullable', 'integer', 'min:0'],
            'bio' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => UserRole::Doctor,
            'password' => Hash::make($data['password'] ?? 'password'),
        ]);

        Doctor::create([
            'user_id' => $user->id,
            'specialization' => $data['specialization'],
            'license_number' => $data['license_number'],
            'years_of_experience' => $data['years_of_experience'],
            'bio' => $data['bio'],
        ]);

        return redirect()->route('admin.doctors.index')->with('status', 'Doctor created');
    }

    public function edit(Doctor $doctor): View
    {
        Gate::authorize('access-admin');

        $doctor->load('user');

        return view('admin.doctors.edit', compact('doctor'));
    }

    public function update(Request $request, Doctor $doctor): RedirectResponse
    {
        Gate::authorize('access-admin');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$doctor->user_id],
            'password' => ['nullable', 'string', 'min:8'],
            'specialization' => ['required', 'string', 'max:255'],
            'license_number' => ['required', 'string', 'max:255', 'unique:doctors,license_number,'.$doctor->id],
            'years_of_experience' => ['nullable', 'integer', 'min:0'],
            'bio' => ['nullable', 'string'],
        ]);

        $doctor->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => empty($data['password']) ? $doctor->user->password : Hash::make($data['password']),
        ]);

        $doctor->update([
            'specialization' => $data['specialization'],
            'license_number' => $data['license_number'],
            'years_of_experience' => $data['years_of_experience'],
            'bio' => $data['bio'],
        ]);

        return redirect()->route('admin.doctors.index')->with('status', 'Doctor updated');
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        Gate::authorize('access-admin');

        $doctor->user()->delete();
        $doctor->delete();

        return redirect()->route('admin.doctors.index')->with('status', 'Doctor deleted');
    }
}
