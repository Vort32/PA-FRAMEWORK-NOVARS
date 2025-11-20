@extends('layouts.doctor')

@php($title = 'My Reports')

@section('content')
    <x-table :headers="['Operation', 'Patient', 'Outcome', 'Duration', 'Updated', 'Actions']">
        @forelse ($reports as $report)
            <tr class="text-sm text-gray-700">
                <td class="px-4 py-3">{{ $report->operation?->scheduled_at?->format('d M Y H:i') }}</td>
                <td class="px-4 py-3">{{ $report->operation?->patient?->name }}</td>
                <td class="px-4 py-3 capitalize">{{ $report->status_outcome->value ?? $report->status_outcome }}</td>
                <td class="px-4 py-3">{{ $report->duration_minutes }} mins</td>
                <td class="px-4 py-3">{{ $report->updated_at->diffForHumans() }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('doctor.reports.create', $report->operation) }}" class="rounded bg-[#38B2AC]/20 px-3 py-1 text-xs font-semibold text-[#2B6CB0]">Edit</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">No reports submitted.</td>
            </tr>
        @endforelse
        <tr>
            <td colspan="6" class="px-4 py-4">
                {{ $reports->links() }}
            </td>
        </tr>
    </x-table>
@endsection
