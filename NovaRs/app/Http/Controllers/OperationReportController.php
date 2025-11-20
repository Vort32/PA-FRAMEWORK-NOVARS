<?php

namespace App\Http\Controllers;

use App\Enums\OperationOutcomeStatus;
use App\Models\Operation;
use App\Models\OperationReport;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OperationReportController extends Controller
{
    public function create(Operation $operation): View
    {
        Gate::authorize('submit-operation-report', $operation);

        $operation->load(['patient', 'room']);

        $report = $operation->report;

        return view('doctor.reports.form', [
            'operation' => $operation,
            'report' => $report,
            'outcomes' => OperationOutcomeStatus::cases(),
        ]);
    }

    public function store(Request $request, Operation $operation): RedirectResponse|JsonResponse
    {
        Gate::authorize('submit-operation-report', $operation);

        $data = $this->validateReport($request);

        $report = OperationReport::updateOrCreate(
            ['operation_id' => $operation->id],
            array_merge($data, [
                'doctor_id' => $operation->doctor_id,
            ])
        );

        $operation->update(['status' => 'completed']);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Operation report saved',
                'report' => $report->load('operation.patient'),
            ]);
        }

        return redirect()->route('doctor.dashboard')->with('status', 'Operation report saved');
    }

    public function update(Request $request, OperationReport $operationReport): RedirectResponse|JsonResponse
    {
        Gate::authorize('update', $operationReport);

        $data = $this->validateReport($request);

        $operationReport->update($data);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Operation report updated',
                'report' => $operationReport->fresh()->load('operation.patient'),
            ]);
        }

        return redirect()->route('doctor.dashboard')->with('status', 'Operation report updated');
    }

    protected function validateReport(Request $request): array
    {
        return $request->validate([
            'status_outcome' => ['required', 'in:'.implode(',', OperationOutcomeStatus::values())],
            'complications' => ['nullable', 'string'],
            'procedure_details' => ['required', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
        ]);
    }
}
