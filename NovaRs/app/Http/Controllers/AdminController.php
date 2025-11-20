<?php

namespace App\Http\Controllers;

use App\Enums\OperationRequestStatus;
use App\Enums\OperationStatus;
use App\Enums\RoomStatus;
use App\Enums\UserRole;
use App\Models\Doctor;
use App\Models\Operation;
use App\Models\OperationRequest;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        Gate::authorize('access-admin');

        $operationsPerMonth = Operation::query()
            ->selectRaw('DATE_FORMAT(scheduled_at, "%Y-%m") as period, COUNT(*) as total')
            ->where('scheduled_at', '>=', Carbon::now()->startOfMonth()->subMonths(5))
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $weeklySchedule = Operation::query()
            ->whereBetween('scheduled_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->orderBy('scheduled_at')
            ->with(['patient', 'doctor', 'room'])
            ->get();

        $upcomingOperations = Operation::query()
            ->where('scheduled_at', '>=', Carbon::now())
            ->orderBy('scheduled_at')
            ->limit(5)
            ->with(['patient', 'doctor', 'room'])
            ->get();

        $recentDoctors = Doctor::with('user')
            ->latest()
            ->limit(8)
            ->get();

        $awaitingSchedulingCount = OperationRequest::where('status', OperationRequestStatus::Approved)
            ->whereNull('operation_id')
            ->count();

        $recentRequests = OperationRequest::with(['patient', 'doctor'])
            ->where('status', OperationRequestStatus::Approved)
            ->whereNull('operation_id')
            ->latest()
            ->limit(5)
            ->get();

        $metrics = [
            'total_operations' => Operation::count(),
            'operations_completed' => Operation::where('status', OperationStatus::Completed)->count(),
            'active_doctors' => User::where('role', UserRole::Doctor->value)->count(),
            'rooms_in_use' => Room::where('status', RoomStatus::InUse->value)->count(),
            'requests_awaiting_scheduling' => $awaitingSchedulingCount,
        ];

        return view('admin.dashboard', [
            'operationsPerMonth' => $operationsPerMonth,
            'weeklySchedule' => $weeklySchedule,
            'upcomingOperations' => $upcomingOperations,
            'metrics' => $metrics,
            'recentDoctors' => $recentDoctors,
            'recentRequests' => $recentRequests,
        ]);
    }
}
