@extends('layouts.doctor')

@php
    $title = 'Operation Report';
@endphp

@section('content')
    <x-card title="Operation Details">
        <div class="grid gap-4 md:grid-cols-2 text-sm text-gray-700">
            <div>
                <p class="font-semibold text-gray-900">Patient</p>
                <p>{{ $operation->patient?->name }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-900">Room</p>
                <p>{{ $operation->room?->name }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-900">Scheduled At</p>
                <p>{{ $operation->scheduled_at->format('d M Y H:i') }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-900">Estimated Duration</p>
                <p>{{ $operation->estimated_duration_minutes }} mins</p>
            </div>
        </div>
    </x-card>

    <x-card title="{{ $report ? 'Update' : 'Submit' }} Operation Report">
        <form action="{{ $report ? route('doctor.reports.update', ['operationReport' => $report], false) : route('doctor.reports.store', ['operation' => $operation], false) }}" method="POST" class="space-y-5">
            @csrf
            @if ($report)
                @method('PUT')
            @endif

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    @php
                        $outcomeOptions = collect($outcomes)->mapWithKeys(function ($outcome) {
                            return [$outcome->value => \Illuminate\Support\Str::headline($outcome->value)];
                        })->toArray();

                        $reportOutcome = $report?->status_outcome;
                        if ($reportOutcome instanceof \BackedEnum) {
                            $reportOutcome = $reportOutcome->value;
                        }

                        $selectedOutcome = old('status_outcome', $reportOutcome);
                    @endphp
                    <x-ui.select label="Outcome" model="status_outcome" :options="$outcomeOptions" :selected="$selectedOutcome" />
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" min="1" value="{{ old('duration_minutes', $report->duration_minutes ?? $operation->estimated_duration_minutes) }}" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
                </div>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Complications</label>
                <textarea name="complications" rows="2" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">{{ old('complications', $report->complications ?? '') }}</textarea>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Procedure Details</label>
                <textarea name="procedure_details" rows="5" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">{{ old('procedure_details', $report->procedure_details ?? '') }}</textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('doctor.operations') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600">Cancel</a>
                <button type="submit" class="rounded-lg bg-[#2B6CB0] px-4 py-2 text-sm font-semibold text-white hover:bg-[#1E4E82]">{{ $report ? 'Update' : 'Submit' }} Report</button>
            </div>
        </form>
    </x-card>
@endsection
