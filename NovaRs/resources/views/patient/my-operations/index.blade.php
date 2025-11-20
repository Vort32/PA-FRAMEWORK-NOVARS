@extends('layouts.patient')

@php
    $title = 'My Operations';
@endphp

@section('content')
    <x-table :headers="['Scheduled At', 'Doctor', 'Room', 'Status', 'Report']">
        @forelse ($operations as $operation)
            <tr class="text-sm text-gray-700">
                <td class="px-4 py-3">{{ $operation->scheduled_at->format('d M Y H:i') }}</td>
                <td class="px-4 py-3">{{ $operation->doctor?->name }}</td>
                <td class="px-4 py-3">{{ $operation->room?->name }}</td>
                <td class="px-4 py-3">
                    @if ($operation->report)
                        @php
                            $outcomeValue = $operation->report->status_outcome instanceof \BackedEnum
                                ? $operation->report->status_outcome->value
                                : $operation->report->status_outcome;
                        @endphp
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                            <i data-lucide="activity" class="h-3.5 w-3.5"></i>
                            {{ \Illuminate\Support\Str::headline($outcomeValue) }}
                        </span>
                    @else
                        <span class="capitalize">{{ \Illuminate\Support\Str::headline($operation->status->value ?? $operation->status) }}</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if ($operation->report)
                        <a href="{{ route('patient.reports.show', $operation) }}" class="rounded bg-[#38B2AC]/20 px-3 py-1 text-xs font-semibold text-[#2B6CB0]">View</a>
                    @else
                        <span class="text-xs text-gray-500">Pending</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">No operations recorded.</td>
            </tr>
        @endforelse
        <tr>
            <td colspan="5" class="px-4 py-4">
                {{ $operations->links() }}
            </td>
        </tr>
    </x-table>
@endsection
