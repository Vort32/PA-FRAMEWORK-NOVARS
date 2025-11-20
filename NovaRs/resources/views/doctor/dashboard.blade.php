@extends('layouts.doctor')

@php
    $title = 'Doctor Dashboard';
    $todayCount = $todayOperations->count();
    $pendingReports = $recentReports->where('status_outcome', 'pending')->count();
    $statuses = ['scheduled', 'ongoing', 'completed', 'postponed', 'cancelled'];
    $referralPendingCount = $referralRequests->where('status', \App\Enums\OperationRequestStatus::Pending)->count();
@endphp

@section('content')
    <div class="space-y-8">
        <section class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-xl dark:border-slate-700 dark:bg-slate-900 lg:col-span-2">
                <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div class="space-y-2">
                        <p class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500 dark:border-slate-700 dark:text-slate-400">
                            <i data-lucide="activity" class="h-4 w-4"></i>
                            Today
                        </p>
                        <h2 class="text-3xl font-semibold text-slate-900 dark:text-white">Critical operations overview</h2>
                        <p class="text-sm text-slate-600 dark:text-slate-300">Stay focused on upcoming surgeries, patient flow, and post-op documentation.</p>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-center dark:border-slate-700 dark:bg-slate-800">
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-300">Operations today</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ $todayCount }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 text-center shadow-sm dark:border-slate-700 dark:bg-slate-900">
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-300">Pending reports</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ $pendingReports }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl dark:border-slate-700 dark:bg-slate-900">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Status snapshot</h3>
                <ul class="mt-4 space-y-3">
                    @foreach ($statuses as $status)
                        @php
                            $value = $operationSummary[$status] ?? 0;
                            $badgeClasses = [
                                'scheduled' => 'bg-blue-100 text-blue-800',
                                'ongoing' => 'bg-yellow-100 text-yellow-700',
                                'completed' => 'bg-green-100 text-green-700',
                                'postponed' => 'bg-orange-100 text-orange-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                            ][$status];
                        @endphp
                        <li class="flex items-center justify-between rounded-xl border border-slate-200/80 px-4 py-3 text-sm font-medium text-slate-700 shadow-sm dark:border-slate-700 dark:text-slate-200">
                            <div class="flex items-center gap-2 capitalize">
                                <i data-lucide="target" class="h-4 w-4 text-slate-500 dark:text-slate-300"></i>
                                {{ $status }}
                            </div>
                            <span class="inline-flex min-w-[3rem] items-center justify-center rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClasses }}">{{ $value }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        <x-card title="Patient Referrals">
            <div class="mb-4 flex items-center justify-between text-sm text-slate-600 dark:text-slate-300">
                <p>Referrals submitted directly to you by patients. Review and approve them promptly.</p>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 dark:border-slate-700 dark:text-slate-200">Pending: {{ $referralPendingCount }}</span>
            </div>
            <x-table :headers="['Submitted', 'Patient', 'Suspected Disease', 'Status', 'Referral Letter', 'Actions']">
                @forelse ($referralRequests as $referral)
                    @php
                        $statusValue = $referral->status->value ?? $referral->status;
                        $statusClasses = [
                            'pending' => 'bg-yellow-100 text-yellow-700 border border-yellow-300',
                            'approved' => 'bg-blue-100 text-blue-700 border border-blue-300',
                        ][$statusValue] ?? 'bg-slate-100 text-slate-700 border border-slate-200';
                    @endphp
                    <tr class="text-sm text-slate-800 odd:bg-white even:bg-slate-50 hover:bg-slate-100 dark:text-slate-100 dark:odd:bg-slate-900 dark:even:bg-slate-800 dark:hover:bg-slate-700">
                        <td class="px-4 py-3 font-medium">{{ $referral->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $referral->patient?->name }}</td>
                        <td class="px-4 py-3">{{ $referral->disease?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses }}">
                                <i data-lucide="activity" class="h-3.5 w-3.5"></i>
                                {{ \Illuminate\Support\Str::headline($statusValue) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($referral->referral_letter_path)
                                <a href="{{ route('operation-requests.referral-letter', [$referral], false) }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 px-3 py-1 text-xs font-semibold text-[#2B6CB0] hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200">
                                    <i data-lucide="paperclip" class="h-3.5 w-3.5"></i>
                                    Letter
                                </a>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('doctor.operation-requests.show', [$referral], false) }}" class="inline-flex items-center gap-1.5 rounded-full border border-[#2B6CB0] px-3 py-1 text-xs font-semibold text-[#2B6CB0] transition hover:bg-[#2B6CB0] hover:text-white">
                                <i data-lucide="arrow-up-right" class="h-3.5 w-3.5"></i>
                                Review
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-sm text-slate-500 dark:text-slate-400">No referrals assigned to you yet.</td>
                    </tr>
                @endforelse
            </x-table>
        </x-card>

        <x-card title="Today's Operations">
            <x-table :headers="['Time', 'Patient', 'Room', 'Status']">
                @forelse ($todayOperations as $operation)
                    @php
                        $statusValue = strtolower($operation->status->value ?? $operation->status);
                        $statusClasses = [
                            'scheduled' => 'bg-blue-100 text-blue-800 border border-blue-200',
                            'ongoing' => 'bg-yellow-100 text-yellow-700 border border-yellow-200',
                            'completed' => 'bg-green-100 text-green-700 border border-green-200',
                            'postponed' => 'bg-orange-100 text-orange-700 border border-orange-200',
                            'cancelled' => 'bg-red-100 text-red-700 border border-red-200',
                        ][$statusValue] ?? 'bg-slate-200 text-slate-700 border border-slate-300';
                    @endphp
                    <tr class="text-sm text-slate-800 odd:bg-white even:bg-slate-50 hover:bg-slate-100 dark:text-slate-100 dark:odd:bg-slate-900 dark:even:bg-slate-800 dark:hover:bg-slate-700">
                        <td class="px-4 py-3 font-medium">{{ $operation->scheduled_at->format('H:i') }}</td>
                        <td class="px-4 py-3">{{ $operation->patient?->name }}</td>
                        <td class="px-4 py-3">{{ $operation->room?->name }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses }}">
                                <i data-lucide="pulse" class="h-3.5 w-3.5"></i>
                                {{ \Illuminate\Support\Str::headline($operation->status->value ?? $operation->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-center text-sm text-slate-500 dark:text-slate-400">No operations scheduled today.</td>
                    </tr>
                @endforelse
            </x-table>
        </x-card>

        <x-card title="Recent Reports">
            <x-table :headers="['Operation', 'Patient', 'Outcome', 'Duration', 'Created']">
                @forelse ($recentReports as $report)
                    <tr class="text-sm text-slate-800 odd:bg-white even:bg-slate-50 hover:bg-slate-100 dark:text-slate-100 dark:odd:bg-slate-900 dark:even:bg-slate-800 dark:hover:bg-slate-700">
                        <td class="px-4 py-3 font-medium">{{ optional($report->operation?->scheduled_at)->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $report->operation?->patient?->name }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-300 bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200">
                                <i data-lucide="check-circle-2" class="h-3.5 w-3.5"></i>
                                {{ \Illuminate\Support\Str::headline($report->status_outcome->value ?? $report->status_outcome) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $report->duration_minutes }} mins</td>
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-300">{{ $report->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-sm text-slate-500 dark:text-slate-400">No reports submitted yet.</td>
                    </tr>
                @endforelse
            </x-table>
        </x-card>
    </div>
@endsection
