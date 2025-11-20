@extends('layouts.admin')

@php
    $title = 'Admin Dashboard';
    $statIcons = [
        'total_patients' => 'users',
        'total_doctors' => 'stethoscope',
        'total_staff' => 'user-circle-2',
        'total_rooms' => 'building-2',
        'total_operations' => 'activity',
        'requests_awaiting_scheduling' => 'inbox',
        'equipment_ready' => 'shield-check',
        'diseases_managed' => 'virus',
    ];
@endphp

@section('content')
    <div class="space-y-10">
        <section class="grid gap-6 xl:grid-cols-[2fr,1fr]">
            <div class="rounded-3xl border border-teal-200 bg-gradient-to-br from-white via-teal-50 to-teal-50/40 p-8 shadow-xl dark:border-slate-700 dark:from-slate-900 dark:via-slate-900 dark:to-slate-900">
                <div class="flex flex-col gap-8">
                    <header class="space-y-3">
                        <p class="inline-flex items-center gap-2 rounded-full border border-teal-200/70 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.32em] text-teal-600 dark:border-slate-700 dark:bg-slate-800 dark:text-teal-300">
                            <i data-lucide="shield-check" class="h-4 w-4"></i>
                            Operations Center
                        </p>
                        <h2 class="text-3xl font-semibold text-slate-900 dark:text-white">Welcome back, {{ auth()->user()->name ?? 'Administrator' }}</h2>
                        <p class="text-sm text-slate-600 dark:text-slate-300">Monitor hospital activity, coordinate surgical teams, and ensure patient journeys stay on track.</p>
                    </header>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($metrics as $label => $value)
                            @php
                                $normalizedLabel = \Illuminate\Support\Str::snake($label);
                                $icon = $statIcons[$normalizedLabel] ?? 'bar-chart-3';
                                $display = \Illuminate\Support\Str::headline($label);
                            @endphp
                            <div class="rounded-2xl border border-teal-200 bg-white p-5 shadow-lg dark:border-slate-700 dark:bg-slate-800">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-teal-700 dark:text-teal-200">{{ $display }}</p>
                                    <i data-lucide="{{ $icon }}" class="h-5 w-5 text-teal-500"></i>
                                </div>
                                <p class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">{{ $value }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-teal-200 bg-white p-6 shadow-xl dark:border-slate-700 dark:bg-slate-900">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-teal-600 dark:text-teal-300">Today</p>
                <div class="mt-4 space-y-3">
                    @forelse ($upcomingOperations->take(3) as $operation)
                        <div class="rounded-2xl border border-teal-100 bg-teal-50/80 px-4 py-3 text-sm text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-100">
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $operation->scheduled_at->format('H:i') }} · {{ $operation->room?->name ?? 'Room TBD' }}</p>
                            <p class="text-xs text-slate-600 dark:text-slate-300">{{ $operation->patient?->name }} with {{ $operation->doctor?->name ?? 'Doctor TBD' }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-300">No operations scheduled for today.</p>
                    @endforelse
                </div>
                <a href="{{ route('admin.operations.index') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl border border-teal-200 px-4 py-2 text-xs font-semibold text-teal-700 shadow-sm transition hover:bg-teal-50 hover:text-teal-800 dark:border-slate-600 dark:text-teal-200 dark:hover:bg-slate-800">
                    <i data-lucide="calendar-days" class="h-4 w-4"></i>
                    View full schedule
                </a>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-3">
            <x-card title="Operations in the Last 6 Months">
                <div class="flex items-end gap-5">
                    @forelse ($operationsPerMonth as $item)
                        <div class="flex flex-col items-center text-xs font-medium text-slate-500 dark:text-slate-300">
                            <div class="flex h-32 w-10 items-end justify-center overflow-hidden rounded-xl bg-teal-50 dark:bg-slate-800/60">
                                <div class="w-full rounded-xl bg-gradient-to-t from-teal-500 via-emerald-400 to-transparent" style="height: {{ max($item->total, 1) * 12 }}px"></div>
                            </div>
                            <span class="mt-2 uppercase tracking-[0.2em] text-[10px] text-slate-400">{{ $item->period }}</span>
                            <span class="text-sm font-semibold text-slate-700 dark:text-white">{{ $item->total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No data available.</p>
                    @endforelse
                </div>
            </x-card>

            <x-card title="Upcoming Operations">
                <ul class="space-y-3">
                    @forelse ($upcomingOperations as $operation)
                        @php
                            $statusValue = strtolower($operation->status->value ?? $operation->status);
                            $statusClasses = [
                                'scheduled' => 'bg-blue-100 text-blue-600 border border-blue-200',
                                'ongoing' => 'bg-yellow-100 text-yellow-600 border border-yellow-200',
                                'completed' => 'bg-green-100 text-green-600 border border-green-200',
                                'postponed' => 'bg-orange-100 text-orange-600 border border-orange-200',
                                'cancelled' => 'bg-red-100 text-red-600 border border-red-200',
                            ][$statusValue] ?? 'bg-slate-200 text-slate-700 border border-slate-300';
                        @endphp
                        <li class="flex items-start justify-between gap-4 rounded-2xl border border-teal-100 bg-white px-4 py-3 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                            <div class="space-y-1 text-sm text-slate-700 dark:text-slate-200">
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $operation->scheduled_at->format('d M Y · H:i') }}</p>
                                <p>{{ $operation->patient?->name }} with {{ $operation->doctor?->name ?? 'Doctor TBD' }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-300">Room: {{ $operation->room?->name ?? 'TBD' }}</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses }}">
                                <i data-lucide="pulse" class="h-3.5 w-3.5"></i>
                                {{ \Illuminate\Support\Str::headline($operation->status->value ?? $operation->status) }}
                            </span>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">No upcoming operations.</li>
                    @endforelse
                </ul>
            </x-card>

            <x-card title="Operation Requests Awaiting Scheduling">
                <ul class="space-y-3">
                    @forelse ($recentRequests as $request)
                        <li class="flex items-start justify-between gap-3 rounded-2xl border border-teal-100 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                            <div class="space-y-1">
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $request->patient?->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-300">Submitted {{ $request->created_at->diffForHumans() }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-300">Preferred: {{ $request->preferred_date?->format('d M Y') ?? '—' }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-300">Doctor: {{ $request->doctor?->name ?? '—' }}</p>
                            </div>
                            <a href="{{ route('admin.operation-requests.show', [$request], false) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-teal-600 px-4 py-1.5 text-xs font-semibold text-white shadow-md transition hover:bg-teal-700">
                                <i data-lucide="arrow-up-right" class="h-3.5 w-3.5"></i>
                                Review
                            </a>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">No pending requests.</li>
                    @endforelse
                </ul>
                <div class="mt-4 flex justify-end">
                    <a href="{{ route('admin.operation-requests.index', [], false) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-teal-700 transition hover:text-teal-800 dark:text-teal-300 dark:hover:text-teal-200">
                        Manage requests
                        <i data-lucide="chevron-right" class="h-4 w-4"></i>
                    </a>
                </div>
            </x-card>
        </section>

        <x-card title="Weekly Schedule">
            <x-table :headers="['Date', 'Patient', 'Doctor', 'Room', 'Status']">
                @forelse ($weeklySchedule as $operation)
                    @php
                        $statusValue = strtolower($operation->status->value ?? $operation->status);
                        $badgeClasses = [
                            'scheduled' => 'bg-blue-100 text-blue-600 border border-blue-200',
                            'ongoing' => 'bg-yellow-100 text-yellow-600 border border-yellow-200',
                            'completed' => 'bg-green-100 text-green-600 border border-green-200',
                            'postponed' => 'bg-orange-100 text-orange-600 border border-orange-200',
                            'cancelled' => 'bg-red-100 text-red-600 border border-red-200',
                        ][$statusValue] ?? 'bg-slate-200 text-slate-700 border border-slate-300';
                    @endphp
                    <tr class="text-sm text-slate-700 odd:bg-white even:bg-slate-50 hover:bg-slate-100 dark:text-slate-100 dark:odd:bg-slate-900 dark:even:bg-slate-800 dark:hover:bg-slate-700">
                        <td class="px-4 py-3 font-medium">{{ $operation->scheduled_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $operation->patient?->name }}</td>
                        <td class="px-4 py-3">{{ $operation->doctor?->name ?? 'TBD' }}</td>
                        <td class="px-4 py-3">{{ $operation->room?->name ?? 'TBD' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClasses }}">
                                <i data-lucide="badge-check" class="h-3.5 w-3.5"></i>
                                {{ \Illuminate\Support\Str::headline($operation->status->value ?? $operation->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-sm text-slate-500">No operations scheduled for this week.</td>
                    </tr>
                @endforelse
            </x-table>
        </x-card>

        <x-card title="Doctor Roster">
            <x-table :headers="['Name', 'Email', 'Specialization', 'Experience', 'Joined']">
                @forelse ($recentDoctors as $doctor)
                    <tr class="text-sm text-slate-700 odd:bg-white even:bg-slate-50 hover:bg-slate-100 dark:text-slate-100 dark:odd:bg-slate-900 dark:even:bg-slate-800 dark:hover:bg-slate-700">
                        <td class="px-4 py-3 font-medium">{{ $doctor->user?->name }}</td>
                        <td class="px-4 py-3">{{ $doctor->user?->email }}</td>
                        <td class="px-4 py-3">{{ $doctor->specialization }}</td>
                        <td class="px-4 py-3">{{ $doctor->years_of_experience ? $doctor->years_of_experience.' yrs' : '—' }}</td>
                        <td class="px-4 py-3">{{ optional($doctor->created_at)->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-sm text-slate-500">No doctors registered.</td>
                    </tr>
                @endforelse
            </x-table>

            <div class="mt-4 flex justify-end">
                <a href="{{ route('admin.doctors.index', [], false) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-teal-200 px-4 py-2 text-sm font-medium text-teal-700 transition hover:bg-teal-50 hover:text-teal-800 dark:border-slate-600 dark:text-teal-200 dark:hover:bg-slate-800">
                    Manage Doctors
                    <i data-lucide="arrow-up-right" class="h-4 w-4"></i>
                </a>
            </div>
        </x-card>
    </div>
@endsection
