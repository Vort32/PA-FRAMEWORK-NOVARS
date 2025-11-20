@extends('layouts.patient')

@php($title = 'Operation Report')

@section('content')
    <x-card title="Operation Overview">
        <div class="grid gap-4 md:grid-cols-2 text-sm text-gray-700">
            <div>
                <p class="font-semibold text-gray-900">Scheduled</p>
                <p>{{ $operation->scheduled_at->format('d M Y H:i') }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-900">Doctor</p>
                <p>{{ $operation->doctor?->name }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-900">Room</p>
                <p>{{ $operation->room?->name }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-900">Status</p>
                <p class="capitalize">{{ $operation->status->value ?? $operation->status }}</p>
            </div>
        </div>
    </x-card>

    <x-card title="Report Details">
        @if ($operation->report)
            <dl class="grid gap-4 md:grid-cols-2 text-sm text-gray-700">
                <div>
                    <dt class="font-semibold text-gray-900">Outcome</dt>
                    <dd class="capitalize">{{ $operation->report->status_outcome->value ?? $operation->report->status_outcome }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-900">Duration</dt>
                    <dd>{{ $operation->report->duration_minutes }} minutes</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="font-semibold text-gray-900">Complications</dt>
                    <dd>{{ $operation->report->complications ?: 'None reported.' }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="font-semibold text-gray-900">Procedure Details</dt>
                    <dd class="whitespace-pre-line">{{ $operation->report->procedure_details }}</dd>
                </div>
            </dl>
        @else
            <p class="text-sm text-gray-500">Your report is not available yet. Please check back later.</p>
        @endif

        <div class="mt-6">
            <a href="{{ url()->previous() }}" class="inline-flex items-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600">Back</a>
        </div>
    </x-card>
@endsection
