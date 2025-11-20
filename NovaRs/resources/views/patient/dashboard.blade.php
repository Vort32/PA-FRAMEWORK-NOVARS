@extends('layouts.patient')

@php
    $title = 'Patient Dashboard';
    $upcomingCount = $operations->where('scheduled_at', '>=', now())->count();
    $completedCount = $operations->where('status', 'completed')->count();

    $operationRequestsByStatus = $operationRequests->groupBy(function ($request) {
        return $request->status instanceof \BackedEnum ? $request->status->value : $request->status;
    });

    $operationsByStatus = $operations->groupBy(function ($operation) {
        return $operation->status instanceof \BackedEnum ? $operation->status->value : $operation->status;
    });

    $pendingRequests = $operationRequestsByStatus->get('pending', collect())->count();
    $approvedRequests = $operationRequestsByStatus->get('approved', collect())->count();
    $scheduledRequests = $operationRequestsByStatus->get('scheduled', collect())->count();
    $readyRequests = $approvedRequests + $scheduledRequests;
    $totalRequests = $operationRequests->count();
    $activeRequests = $pendingRequests + $readyRequests;

    $nextOperation = $operations->where('scheduled_at', '>=', now())->sortBy('scheduled_at')->first();
    $operationsToday = $operations->filter(fn ($operation) => optional($operation->scheduled_at)->isToday())->count();
    $completedThisMonth = $operationsByStatus->get('completed', collect())->filter(fn ($operation) => optional($operation->scheduled_at)->isSameMonth(now()))->count();
    $lastRequestUpdate = optional($operationRequests->sortByDesc('updated_at')->first()?->updated_at)?->diffForHumans();
@endphp

@section('content')
    <div class="space-y-8">
        <section class="relative overflow-hidden rounded-3xl border border-emerald-100 bg-gradient-to-br from-emerald-50 via-emerald-100 to-white p-8 text-emerald-900 shadow-xl sm:p-10">
            <div class="pointer-events-none absolute -left-28 top-8 h-60 w-60 rounded-full bg-emerald-200/40 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-20 -bottom-20 h-64 w-64 rounded-full bg-teal-200/30 blur-3xl"></div>
            <div class="pointer-events-none absolute left-1/2 top-1/3 h-32 w-32 -translate-x-1/2 rounded-full bg-white/50 blur-2xl"></div>

            <div class="relative grid gap-10 lg:grid-cols-[minmax(0,1fr)_320px]">
                <div class="space-y-6">
                    <p class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-emerald-600 shadow-sm">
                        <i data-lucide="heart-pulse" class="h-4 w-4"></i>
                        Care Journey
                    </p>

                    <div class="space-y-4">
                        <h1 class="text-3xl font-semibold leading-tight text-emerald-900 sm:text-4xl">Welcome back, {{ auth()->user()->name ?? 'Patient' }}</h1>
                        <p class="max-w-2xl text-sm text-emerald-700/80 sm:text-base">Track your surgery requests, review schedules, and stay confident about every upcoming procedure.</p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('patient.operation-requests.create', [], false) }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-emerald-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500/70">
                            <i data-lucide="plus" class="h-4 w-4"></i>
                            Request Surgery
                        </a>
                        <a href="{{ route('patient.operations') }}" class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-white/70 px-4 py-2.5 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-200/70">
                            <i data-lucide="stethoscope" class="h-4 w-4"></i>
                            My Operations
                        </a>
                        <a href="{{ route('patient.operations') }}#reports" class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-white/60 px-4 py-2.5 text-sm font-semibold text-emerald-600 shadow-sm transition hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-200/70">
                            <i data-lucide="file-text" class="h-4 w-4"></i>
                            Reports
                        </a>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl border border-emerald-100 bg-white px-5 py-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-500">Upcoming operations</p>
                            <p class="mt-2 flex items-baseline gap-2 text-3xl font-semibold text-emerald-800">
                                {{ $upcomingCount }}
                                <span class="text-xs font-medium uppercase tracking-wider text-emerald-500">booked</span>
                            </p>
                            <p class="mt-1 text-xs text-emerald-600/70">{{ $operationsToday }} scheduled for today</p>
                        </div>
                        <div class="rounded-2xl border border-emerald-100 bg-white px-5 py-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-500">Completed operations</p>
                            <p class="mt-2 flex items-baseline gap-2 text-3xl font-semibold text-emerald-800">
                                {{ $completedCount }}
                                <span class="text-xs font-medium uppercase tracking-wider text-emerald-500">total</span>
                            </p>
                            <p class="mt-1 text-xs text-emerald-600/70">{{ $completedThisMonth }} finished this month</p>
                        </div>
                        <div class="rounded-2xl border border-emerald-100 bg-white px-5 py-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-500">Active requests</p>
                            <p class="mt-2 flex items-baseline gap-2 text-3xl font-semibold text-emerald-800">
                                {{ $activeRequests }}
                                <span class="text-xs font-medium uppercase tracking-wider text-emerald-500">in progress</span>
                            </p>
                            <p class="mt-1 text-xs text-emerald-600/70">{{ $pendingRequests }} waiting approval</p>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                    <div class="rounded-2xl border border-emerald-100 bg-white px-6 py-5 text-emerald-800 shadow-sm">
                        <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-emerald-500">
                            <i data-lucide="calendar-clock" class="h-4 w-4"></i>
                            Next operation
                        </p>
                        @if ($nextOperation)
                            <p class="mt-3 text-2xl font-semibold leading-tight text-emerald-900">{{ $nextOperation->scheduled_at?->format('d M Y • H:i') }}</p>
                            <p class="mt-2 text-sm text-emerald-600">With {{ $nextOperation->doctor?->name ?? 'Doctor assigned soon' }}</p>
                            <div class="mt-4 flex flex-wrap gap-2 text-xs text-emerald-600/80">
                                @if ($nextOperation->room?->name)
                                    <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1">
                                        <i data-lucide="building-2" class="h-3.5 w-3.5"></i>
                                        Room {{ $nextOperation->room?->name }}
                                    </span>
                                @endif
                                @if ($nextOperation->status)
                                    @php
                                        $nextStatus = $nextOperation->status instanceof \BackedEnum ? $nextOperation->status->value : $nextOperation->status;
                                    @endphp
                                    <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1">
                                        <i data-lucide="activity" class="h-3.5 w-3.5"></i>
                                        {{ \Illuminate\Support\Str::headline($nextStatus) }}
                                    </span>
                                @endif
                            </div>
                        @else
                            <p class="mt-3 text-xl font-semibold text-emerald-800">No upcoming operation yet</p>
                            <p class="mt-2 text-sm text-emerald-600">Submit a request or wait for your doctor to schedule your next procedure.</p>
                        @endif
                    </div>
                    <div class="rounded-2xl border border-emerald-100 bg-white px-6 py-5 text-emerald-800 shadow-sm sm:col-span-2 lg:col-span-1">
                        <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-emerald-500">
                            <i data-lucide="bell-ring" class="h-4 w-4"></i>
                            Latest request update
                        </p>
                        <p class="mt-3 text-xl font-semibold text-emerald-900">{{ $lastRequestUpdate ?? 'No activity yet' }}</p>
                        <p class="mt-2 text-sm text-emerald-600">Keep an eye on your operation requests or contact the hospital if you need urgent changes.</p>
                    </div>
                </div>
            </div>
        </section>

        <x-card title="Operation Requests">
            <div class="mb-6 flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                <div class="space-y-4">
                    <p class="text-sm text-emerald-700/80 dark:text-emerald-100/80">Submit a surgery request directly to your preferred doctor and monitor the review status.</p>

                    <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-4">
                        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/80 px-4 py-3">
                            <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-emerald-500">
                                <i data-lucide="clock-3" class="h-3.5 w-3.5"></i>
                                Pending
                            </p>
                            <p class="mt-2 text-2xl font-semibold text-emerald-700">{{ $pendingRequests }}</p>
                        </div>
                        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 px-4 py-3">
                            <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-emerald-500">
                                <i data-lucide="calendar-check" class="h-3.5 w-3.5"></i>
                                Approved / Scheduled
                            </p>
                            <p class="mt-2 text-2xl font-semibold text-emerald-700">{{ $readyRequests }}</p>
                        </div>
                        <div class="rounded-2xl border border-emerald-100 bg-white px-4 py-3 shadow-sm">
                            <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-emerald-500">
                                <i data-lucide="list-checks" class="h-3.5 w-3.5"></i>
                                Total requests
                            </p>
                            <p class="mt-2 text-2xl font-semibold text-emerald-700">{{ $totalRequests }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('patient.operation-requests.create', [], false) }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-emerald-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500">
                        <i data-lucide="plus" class="h-4 w-4"></i>
                        New Request
                    </a>
                    <a href="{{ route('patient.operations') }}" class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-white px-5 py-2.5 text-sm font-semibold text-emerald-600 shadow-sm transition hover:border-emerald-300 hover:text-emerald-700">
                        <i data-lucide="history" class="h-4 w-4"></i>
                        Request History
                    </a>
                </div>
            </div>

            <x-table :headers="['Submitted', 'Doctor', 'Disease', 'Preferred Date', 'Referral Letter', 'Status', 'Notes']">
                @forelse ($operationRequests as $request)
                    @php
                        $requestStatus = $request->status->value;
                        $requestStatusClasses = [
                            'pending' => 'bg-yellow-100 text-yellow-700 border border-yellow-300',
                            'reviewed' => 'bg-blue-100 text-blue-700 border border-blue-300',
                            'approved' => 'bg-blue-100 text-blue-700 border border-blue-300',
                            'scheduled' => 'bg-purple-100 text-purple-700 border border-purple-300',
                            'need_operation' => 'bg-red-100 text-red-700 border border-red-300',
                            'rejected' => 'bg-red-100 text-red-700 border border-red-300',
                        ][$requestStatus] ?? 'bg-emerald-100 text-emerald-700 border border-emerald-300';
                    @endphp
                    <tr class="text-sm text-emerald-900 transition-colors odd:bg-white even:bg-emerald-50/70 hover:bg-emerald-100/60">
                        <td class="px-4 py-3 font-medium">{{ $request->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $request->doctor?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $request->disease?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $request->preferred_date?->format('d M Y') ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($request->referral_letter_path)
                                <a href="{{ route('operation-requests.referral-letter', [$request], false) }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-full border border-emerald-300 bg-white px-3 py-1 text-xs font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-100">
                                    <i data-lucide="paperclip" class="h-3.5 w-3.5"></i>
                                    View Letter
                                </a>
                            @else
                                <span class="text-xs text-emerald-600/70">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold leading-none {{ $requestStatusClasses }}">
                                <i data-lucide="activity" class="h-3.5 w-3.5"></i>
                                {{ \Illuminate\Support\Str::headline($requestStatus) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-emerald-700/80 dark:text-emerald-100/70">
                            @if ($request->status === \App\Enums\OperationRequestStatus::Approved && $request->operation)
                                Scheduled on {{ $request->operation->scheduled_at->format('d M Y H:i') }} — Room {{ $request->operation->room?->name }}
                            @elseif ($request->status === \App\Enums\OperationRequestStatus::Rejected)
                                {{ $request->doctor_notes ?? 'Request rejected' }}
                            @else
                                Awaiting doctor review
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-center text-sm text-emerald-600/60">No operation requests yet.</td>
                    </tr>
                @endforelse
            </x-table>
        </x-card>

        <div class="grid gap-6 lg:grid-cols-2">
            <x-card title="Upcoming Operations">
                <x-table :headers="['Scheduled At', 'Doctor', 'Room', 'Status', 'Actions']">
                    @forelse ($operations->where('scheduled_at', '>=', now()) as $operation)
                        @php
                            if ($operation->report) {
                                $outcomeValue = $operation->report->status_outcome instanceof \BackedEnum
                                    ? $operation->report->status_outcome->value
                                    : $operation->report->status_outcome;

                                $statusValue = $outcomeValue;
                                $operationStatusClasses = [
                                    'success' => 'bg-green-100 text-green-600 border border-green-200',
                                    'complication' => 'bg-yellow-100 text-yellow-600 border border-yellow-200',
                                    'failure' => 'bg-red-100 text-red-600 border border-red-200',
                                ][$outcomeValue] ?? 'bg-emerald-100 text-emerald-700 border border-emerald-200';
                                $statusLabel = 'Outcome: '.\Illuminate\Support\Str::headline($outcomeValue);
                            } else {
                                $statusValue = strtolower($operation->status->value ?? $operation->status);
                                $operationStatusClasses = [
                                    'scheduled' => 'bg-blue-100 text-blue-600 border border-blue-200',
                                    'ongoing' => 'bg-yellow-100 text-yellow-600 border border-yellow-200',
                                    'completed' => 'bg-green-100 text-green-600 border border-green-200',
                                    'postponed' => 'bg-orange-100 text-orange-600 border border-orange-200',
                                    'cancelled' => 'bg-red-100 text-red-600 border border-red-200',
                                ][$statusValue] ?? 'bg-emerald-100 text-emerald-700 border border-emerald-200';
                                $statusLabel = \Illuminate\Support\Str::headline($operation->status->value ?? $operation->status);
                            }
                        @endphp
                        <tr class="text-sm text-emerald-900 transition-colors odd:bg-white even:bg-emerald-50/70 hover:bg-emerald-100/60">
                            <td class="px-4 py-3 font-medium">{{ $operation->scheduled_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3">{{ $operation->doctor?->name }}</td>
                            <td class="px-4 py-3">{{ $operation->room?->name }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $operationStatusClasses }}">
                                    <i data-lucide="stethoscope" class="h-3.5 w-3.5"></i>
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($operation->report)
                                    <a href="{{ route('patient.reports.show', $operation, false) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-300 bg-white px-3 py-1 text-xs font-semibold text-emerald-700 shadow-sm transition hover:border-emerald-400 hover:bg-emerald-50 hover:text-emerald-800">
                                        <i data-lucide="file-text" class="h-3.5 w-3.5"></i>
                                        View Report
                                    </a>
                                @else
                                    <span class="text-xs text-emerald-600/70">Awaiting report</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-sm text-emerald-600/60">No upcoming operations.</td>
                        </tr>
                    @endforelse
                </x-table>
            </x-card>

            <x-card title="Completed Operations">
                <ul class="space-y-3" id="reports">
                    @forelse ($operations->where('status', 'completed') as $operation)
                        <li class="rounded-2xl border border-emerald-100 bg-white/85 px-4 py-4 text-sm text-emerald-800 shadow-sm">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="space-y-1">
                                    <p class="flex items-center gap-2 text-sm font-semibold text-emerald-700">
                                        <i data-lucide="calendar-check" class="h-4 w-4"></i>
                                        {{ $operation->scheduled_at->format('d M Y') }}
                                    </p>
                                    <p class="text-xs text-emerald-600/80">Doctor: {{ $operation->doctor?->name }}</p>
                                    @if ($operation->report)
                                        @php
                                            $outcomeValue = $operation->report->status_outcome instanceof \BackedEnum
                                                ? $operation->report->status_outcome->value
                                                : $operation->report->status_outcome;
                                            $outcomeClasses = [
                                                'success' => 'bg-green-100 text-green-700 border border-green-200',
                                                'complication' => 'bg-yellow-100 text-yellow-700 border border-yellow-200',
                                                'failure' => 'bg-red-100 text-red-700 border border-red-200',
                                            ][$outcomeValue] ?? 'bg-emerald-100 text-emerald-700 border border-emerald-200';
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-semibold {{ $outcomeClasses }}">
                                            <i data-lucide="activity" class="h-3.5 w-3.5"></i>
                                            {{ \Illuminate\Support\Str::headline($outcomeValue) }}
                                        </span>
                                    @endif
                                </div>
                                <a href="{{ route('patient.reports.show', $operation, false) }}" class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl border border-emerald-300 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:border-emerald-400 hover:bg-emerald-100 sm:w-auto">
                                    <i data-lucide="external-link" class="h-3.5 w-3.5"></i>
                                    Report
                                </a>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-emerald-600/70">No completed operations yet.</li>
                    @endforelse
                </ul>
            </x-card>
        </div>
    </div>
@endsection
