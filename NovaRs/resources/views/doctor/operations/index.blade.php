@extends('layouts.doctor')

@php
    $title = 'Operations';
@endphp

@section('content')
    <div class="flex flex-col gap-8">
        <section class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">My Operations</h2>
                <form method="GET" class="flex items-center gap-3">
                    @php
                        $statusOptions = ['' => 'All'] + collect($statuses)->mapWithKeys(function ($status) {
                            return [$status->value => \Illuminate\Support\Str::headline($status->value)];
                        })->toArray();
                    @endphp
                    <x-ui.select label="Status" model="status" :options="$statusOptions" :selected="request('status')" :auto-submit="true" class="min-w-[180px]" />
                </form>
            </div>

            <x-table :headers="['Scheduled At', 'Patient', 'Room', 'Status', 'Actions']">
                @forelse ($assignedOperations as $operation)
                    <tr class="text-sm text-gray-700">
                        <td class="px-4 py-3">{{ $operation->scheduled_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $operation->patient?->name }}</td>
                        <td class="px-4 py-3">{{ $operation->room?->name }}</td>
                        <td class="px-4 py-3 capitalize">{{ $operation->status->value ?? $operation->status }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('doctor.reports.create', $operation) }}" class="rounded bg-[#38B2AC]/20 px-3 py-1 text-xs font-semibold text-[#2B6CB0]">Fill Report</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">No operations assigned.</td>
                    </tr>
                @endforelse
                <tr>
                    <td colspan="5" class="px-4 py-4">
                        {{ $assignedOperations->links() }}
                    </td>
                </tr>
            </x-table>
        </section>

        <section class="flex flex-col gap-4">
            <h2 class="text-lg font-semibold text-gray-800">Pending Requests</h2>
            <x-table :headers="['Scheduled At', 'Patient', 'Room', 'Requested Staff', 'Status']">
                @forelse ($pendingRequests as $operation)
                    <tr class="text-sm text-gray-700">
                        <td class="px-4 py-3">{{ $operation->scheduled_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $operation->patient?->name }}</td>
                        <td class="px-4 py-3">{{ $operation->room?->name }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                @forelse ($operation->staffMembers as $staff)
                                    @if ($staff->pivot->status === App\Enums\OperationStaffStatus::Pending->value)
                                        <span class="rounded-full bg-[#38B2AC]/10 px-3 py-1 text-xs text-[#2B6CB0]">{{ $staff->user?->name }}</span>
                                    @endif
                                @empty
                                    <span class="text-xs text-gray-400">-</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-4 py-3 capitalize text-[#D97706]">Awaiting approval</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">No pending requests.</td>
                    </tr>
                @endforelse
            </x-table>
        </section>

        <section class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">Available Operations</h2>
                <p class="text-sm text-gray-500">Select an operation to request and choose the necessary equipment and support staff.</p>
            </div>
            <x-table :headers="['Scheduled At', 'Patient', 'Room', 'Notes', 'Actions']">
                @forelse ($availableOperations as $operation)
                    <tr class="text-sm text-gray-700">
                        <td class="px-4 py-3">{{ $operation->scheduled_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $operation->patient?->name }}</td>
                        <td class="px-4 py-3">{{ $operation->room?->name }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $operation->notes ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('doctor.operations.request', $operation) }}" class="rounded bg-[#2B6CB0] px-3 py-1 text-xs font-semibold text-white hover:bg-[#1E4E82]">Request</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">No operations available for request.</td>
                    </tr>
                @endforelse
                <tr>
                    <td colspan="5" class="px-4 py-4">
                        {{ $availableOperations->links() }}
                    </td>
                </tr>
            </x-table>
        </section>
    </div>
@endsection
