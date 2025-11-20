<?php

namespace App\Http\Controllers;

use App\Enums\OperationRequestStatus;
use App\Enums\OperationStatus;
use App\Enums\RoomStatus;
use App\Models\Disease;
use App\Models\Operation;
use App\Models\OperationRequest;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AdminOperationRequestController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('access-admin');

        $requests = OperationRequest::with(['patient', 'doctor', 'disease', 'operation.room'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->orderByDesc('created_at')
            ->paginate(12)
            ->appends($request->query());

        return view('admin.operation-requests.index', [
            'requests' => $requests,
            'statuses' => OperationRequestStatus::cases(),
        ]);
    }

    public function show(OperationRequest $operationRequest): View
    {
        Gate::authorize('access-admin');

        $operationRequest->load(['patient', 'patient.patientProfile', 'doctor', 'disease', 'operation.room']);

        return view('admin.operation-requests.show', [
            'request' => $operationRequest,
            'rooms' => Room::orderBy('name')->get()->map(function (Room $room) {
                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    'status' => $room->status,
                ];
            }),
            'diseases' => Disease::orderBy('name')->get(),
        ]);
    }

    public function approve(Request $request, OperationRequest $operationRequest): RedirectResponse
    {
        Gate::authorize('access-admin');

        abort_if($operationRequest->status !== OperationRequestStatus::Approved, 409, 'Request must be approved by the assigned doctor before scheduling.');

        $data = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
            'room_id' => ['required', 'exists:rooms,id'],
            'disease_id' => ['nullable', 'exists:diseases,id'],
            'estimated_duration_minutes' => ['nullable', 'integer', 'min:15'],
            'notes' => ['nullable', 'string'],
            'admin_notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($operationRequest, $data) {
            $operation = Operation::create([
                'patient_id' => $operationRequest->patient_id,
                'doctor_id' => null,
                'requested_doctor_id' => null,
                'room_id' => $data['room_id'],
                'disease_id' => $data['disease_id'] ?? $operationRequest->disease_id,
                'scheduled_at' => Carbon::parse($data['scheduled_at']),
                'status' => OperationStatus::PendingAssignment,
                'estimated_duration_minutes' => $data['estimated_duration_minutes'] ?? 60,
                'notes' => $data['notes'] ?? $operationRequest->symptoms_description,
            ]);

            $operationRequest->update([
                'operation_id' => $operation->id,
                'admin_notes' => $data['admin_notes'] ?? null,
            ]);
        });

        return redirect()->route('admin.operation-requests.index')->with('status', 'Operation request approved and scheduled.');
    }

    public function reject(Request $request, OperationRequest $operationRequest): RedirectResponse
    {
        Gate::authorize('access-admin');

        abort_if(! in_array($operationRequest->status, [OperationRequestStatus::Pending, OperationRequestStatus::Approved], true), 409, 'Request already processed.');

        $data = $request->validate([
            'admin_notes' => ['required', 'string', 'min:5'],
        ]);

        $operationRequest->update([
            'status' => OperationRequestStatus::Rejected,
            'admin_notes' => $data['admin_notes'],
            'rejected_at' => now(),
            'operation_id' => null,
        ]);

        return redirect()->route('admin.operation-requests.index')->with('status', 'Operation request rejected.');
    }
}
