<?php

namespace App\Http\Controllers;

use App\Enums\OperationRequestStatus;
use App\Models\OperationRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DoctorOperationRequestController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('access-doctor');

        $requests = OperationRequest::with(['patient', 'disease'])
            ->where('doctor_id', $request->user()->id)
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->orderByDesc('created_at')
            ->paginate(12)
            ->appends($request->query());

        return view('doctor.operation-requests.index', [
            'requests' => $requests,
            'statuses' => OperationRequestStatus::cases(),
        ]);
    }

    public function show(OperationRequest $operationRequest): View
    {
        Gate::authorize('access-doctor');

        abort_unless($operationRequest->doctor_id === auth()->id(), 403);

        $operationRequest->load(['patient', 'patient.patientProfile', 'disease']);

        return view('doctor.operation-requests.show', [
            'request' => $operationRequest,
        ]);
    }

    public function approve(Request $request, OperationRequest $operationRequest): RedirectResponse
    {
        Gate::authorize('access-doctor');

        abort_unless($operationRequest->doctor_id === auth()->id(), 403);
        abort_if($operationRequest->status !== OperationRequestStatus::Pending, 409, 'Request already processed.');

        $data = $request->validate([
            'doctor_notes' => ['nullable', 'string'],
        ]);

        $operationRequest->update([
            'status' => OperationRequestStatus::Approved,
            'doctor_notes' => $data['doctor_notes'] ?? null,
            'approved_at' => now(),
            'rejected_at' => null,
        ]);

        return redirect()
            ->route('doctor.operation-requests.show', $operationRequest)
            ->with('status', 'Referral approved and forwarded for scheduling.');
    }

    public function reject(Request $request, OperationRequest $operationRequest): RedirectResponse
    {
        Gate::authorize('access-doctor');

        abort_unless($operationRequest->doctor_id === auth()->id(), 403);
        abort_if($operationRequest->status !== OperationRequestStatus::Pending, 409, 'Request already processed.');

        $data = $request->validate([
            'doctor_notes' => ['required', 'string', 'min:5'],
        ]);

        $operationRequest->update([
            'status' => OperationRequestStatus::Rejected,
            'doctor_notes' => $data['doctor_notes'],
            'rejected_at' => now(),
        ]);

        return redirect()
            ->route('doctor.operation-requests.index')
            ->with('status', 'Referral rejected.');
    }
}
