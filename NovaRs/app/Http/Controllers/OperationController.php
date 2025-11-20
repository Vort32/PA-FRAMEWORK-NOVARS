<?php

namespace App\Http\Controllers;

use App\Enums\OperationStaffStatus;
use App\Enums\OperationStatus;
use App\Exports\OperationsExport;
use App\Models\Disease;
use App\Models\Equipment;
use App\Models\Operation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class OperationController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('manage-operations');

        $operations = Operation::with(['patient', 'doctor', 'requestedDoctor', 'room', 'disease', 'staffMembers.user'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('scheduled_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('scheduled_at', '<=', $request->date('to')))
            ->when($request->filled('doctor_id'), fn ($query) => $query->where('doctor_id', $request->integer('doctor_id')))
            ->when($request->filled('room_id'), fn ($query) => $query->where('room_id', $request->integer('room_id')))
            ->orderByDesc('scheduled_at')
            ->paginate(12)
            ->appends($request->query());

        return view('admin.operations.index', [
            'operations' => $operations,
            'statuses' => OperationStatus::cases(),
            'doctors' => User::where('role', 'doctor')->orderBy('name')->get(),
            'rooms' => Room::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('access-admin');

        return view('admin.operations.create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        Gate::authorize('access-admin');

        $data = $this->validateOperation($request);

        $operation = Operation::create($data);

        $this->syncEquipments($operation, $request);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Operation scheduled',
                'operation' => $operation->load(['patient', 'doctor', 'room']),
            ], 201);
        }

        return redirect()->route('admin.operations.index')->with('status', 'Operation scheduled');
    }

    public function edit(Operation $operation): View
    {
        Gate::authorize('access-admin');

        return view('admin.operations.edit', array_merge(
            ['operation' => $operation->load('equipments')],
            $this->formOptions()
        ));
    }

    public function update(Request $request, Operation $operation): RedirectResponse|JsonResponse
    {
        Gate::authorize('access-admin');

        $data = $this->validateOperation($request, $operation);

        $operation->update($data);

        $this->syncEquipments($operation, $request);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Operation updated',
                'operation' => $operation->load(['patient', 'doctor', 'room']),
            ]);
        }

        return redirect()->route('admin.operations.index')->with('status', 'Operation updated');
    }

    public function destroy(Operation $operation): RedirectResponse|JsonResponse
    {
        Gate::authorize('access-admin');

        $operation->equipments()->detach();
        $operation->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Operation deleted']);
        }

        return redirect()->route('admin.operations.index')->with('status', 'Operation deleted');
    }

    public function updateStatus(Request $request, Operation $operation): RedirectResponse|JsonResponse
    {
        Gate::authorize('manage-operations');

        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', OperationStatus::values())],
        ]);

        $operation->update(['status' => $data['status']]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Operation status updated',
                'operation' => $operation,
            ]);
        }

        return redirect()->back()->with('status', 'Operation status updated');
    }

    public function export(Request $request)
    {
        Gate::authorize('access-admin');

        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'doctor_id' => ['nullable', 'exists:users,id'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'status' => ['nullable', 'in:'.implode(',', OperationStatus::values())],
            'format' => ['nullable', 'in:xlsx,pdf'],
        ]);

        $format = $filters['format'] ?? 'xlsx';
        $fileName = 'operation-report-'.Str::slug(now()->toDateTimeString()).'.'.$format;

        $export = new OperationsExport($filters);

        if ($format === 'pdf') {
            return $export->downloadPdf($fileName);
        }

        return Excel::download($export, $fileName);
    }

    protected function validateOperation(Request $request, ?Operation $operation = null): array
    {
        $rules = [
            'patient_id' => ['required', 'exists:users,id'],
            'doctor_id' => ['nullable', 'exists:users,id'],
            'room_id' => ['required', 'exists:rooms,id'],
            'disease_id' => ['nullable', 'exists:diseases,id'],
            'scheduled_at' => ['required', 'date'],
            'status' => ['required', 'in:'.implode(',', OperationStatus::values())],
            'estimated_duration_minutes' => ['nullable', 'integer', 'min:15'],
            'notes' => ['nullable', 'string'],
        ];

        $validated = $request->validate($rules);

        $validated['scheduled_at'] = Carbon::parse($validated['scheduled_at']);

        if (empty($validated['doctor_id'])) {
            $validated['doctor_id'] = null;

            if ($validated['status'] !== OperationStatus::PendingAssignment->value) {
                $validated['status'] = OperationStatus::PendingAssignment->value;
            }
        }

        return $validated;
    }

    protected function formOptions(): array
    {
        return [
            'patients' => User::where('role', 'patient')->orderBy('name')->get(),
            'doctors' => User::where('role', 'doctor')->orderBy('name')->get(),
            'rooms' => Room::orderBy('name')->get(),
            'diseases' => Disease::orderBy('name')->get(),
            'equipments' => Equipment::orderBy('name')->get(),
            'statuses' => OperationStatus::cases(),
        ];
    }

    protected function syncEquipments(Operation $operation, Request $request): void
    {
        $ids = $request->input('equipment_ids', []);
        $quantities = $request->input('equipment_quantities', []);

        $payload = [];
        foreach ($ids as $equipmentId) {
            $quantity = (int) ($quantities[$equipmentId] ?? 1);
            $payload[$equipmentId] = [
                'quantity' => max($quantity, 1),
                'notes' => $request->input('equipment_notes.'.$equipmentId),
            ];
        }

        $operation->equipments()->sync($payload);
    }

    public function approveRequest(Operation $operation): RedirectResponse
    {
        Gate::authorize('access-admin');

        abort_if(! $operation->requested_doctor_id, 404);

        DB::transaction(function () use ($operation) {
            $operation->doctor_id = $operation->requested_doctor_id;
            $operation->requested_doctor_id = null;
            $operation->status = OperationStatus::Scheduled;
            $operation->save();

            DB::table('operation_staff')
                ->where('operation_id', $operation->id)
                ->where('status', OperationStaffStatus::Pending->value)
                ->update(['status' => OperationStaffStatus::Approved->value, 'updated_at' => now()]);
        });

        return redirect()->back()->with('status', 'Operation request approved.');
    }

    public function rejectRequest(Operation $operation): RedirectResponse
    {
        Gate::authorize('access-admin');

        abort_if(! $operation->requested_doctor_id, 404);

        DB::transaction(function () use ($operation) {
            $operation->requested_doctor_id = null;
            $operation->status = OperationStatus::PendingAssignment;
            $operation->save();

            DB::table('operation_staff')
                ->where('operation_id', $operation->id)
                ->where('status', OperationStaffStatus::Pending->value)
                ->delete();
        });

        return redirect()->back()->with('status', 'Operation request rejected.');
    }
}
