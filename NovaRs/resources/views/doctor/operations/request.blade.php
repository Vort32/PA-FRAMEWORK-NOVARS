@extends('layouts.doctor')

@php
    $title = 'Request Operation';
@endphp

@section('content')
    @php
        $selectedEquipments = $operation->equipments->keyBy('id');
        $selectedEquipmentIds = old('equipment_ids', $selectedEquipments->keys()->all());
    @endphp

    <div class="grid gap-6 md:grid-cols-2">
        <x-card title="Operation Details">
            <dl class="space-y-2 text-sm text-gray-700">
                <div class="flex justify-between">
                    <dt class="font-medium text-gray-500">Scheduled At</dt>
                    <dd>{{ $operation->scheduled_at->format('d M Y H:i') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="font-medium text-gray-500">Patient</dt>
                    <dd>{{ $operation->patient?->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="font-medium text-gray-500">Room</dt>
                    <dd>{{ $operation->room?->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="font-medium text-gray-500">Estimated Duration</dt>
                    <dd>{{ $operation->estimated_duration_minutes }} mins</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500">Notes</dt>
                    <dd class="mt-1 text-gray-600">{{ $operation->notes ?? '—' }}</dd>
                </div>
            </dl>
        </x-card>

        <x-card title="Prepare Operation Request">
            <form method="POST" action="{{ route('doctor.operations.request.submit', $operation) }}" class="flex flex-col gap-4">
                @csrf
                <div class="space-y-6">
                    <div>
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">Select Surgical Tools</h3>
                                <p class="mt-1 text-xs text-gray-500">Choose required equipment and specify quantities for the procedure.</p>
                            </div>
                        </div>
                        <div class="mt-3 max-h-72 space-y-3 overflow-y-auto pr-1">
                            @forelse ($equipments as $equipment)
                                @php
                                    $isChecked = in_array($equipment->id, $selectedEquipmentIds);
                                    $pivot = optional($selectedEquipments->get($equipment->id))->pivot;
                                @endphp
                                <div class="rounded-lg border border-gray-200 p-3">
                                    <label class="flex items-start gap-3 text-sm text-gray-700">
                                        <input type="checkbox" name="equipment_ids[]" value="{{ $equipment->id }}" {{ $isChecked ? 'checked' : '' }} class="mt-1 rounded border-gray-300 text-[#2B6CB0] focus:ring-[#2B6CB0]">
                                        <span>
                                            <span class="font-semibold text-gray-900">{{ $equipment->name }}</span>
                                            <span class="block text-xs text-gray-500">Available: {{ $equipment->quantity_available }}</span>
                                            @if ($equipment->category)
                                                <span class="block text-xs text-gray-400">Category: {{ $equipment->category }}</span>
                                            @endif
                                        </span>
                                    </label>
                                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                                        <div>
                                            <label class="text-xs text-gray-500">Quantity</label>
                                            <input type="number" min="1" name="equipment_quantities[{{ $equipment->id }}]" value="{{ old('equipment_quantities.'.$equipment->id, optional($pivot)->quantity ?? 1) }}" class="mt-1 w-full rounded border border-gray-200 px-3 py-2 text-sm focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500">Notes</label>
                                            <input type="text" name="equipment_notes[{{ $equipment->id }}]" value="{{ old('equipment_notes.'.$equipment->id, optional($pivot)->notes ?? '') }}" class="mt-1 w-full rounded border border-gray-200 px-3 py-2 text-sm focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="rounded-lg border border-dashed border-gray-200 px-3 py-4 text-xs text-gray-500">No equipment available to select.</p>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">Select Support Staff</h3>
                                <p class="mt-1 text-xs text-gray-500">Choose staff members to assist during the operation.</p>
                            </div>
                        </div>
                        <div class="mt-3 max-h-64 space-y-3 overflow-y-auto pr-1">
                            @foreach ($staffMembers as $staff)
                                <label class="flex items-start gap-3 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 hover:border-[#2B6CB0]">
                                    <input type="checkbox" name="staff_ids[]" value="{{ $staff->id }}"
                                           {{ in_array($staff->id, old('staff_ids', $selectedStaffIds)) ? 'checked' : '' }}
                                           class="mt-1 h-4 w-4 rounded border-gray-300 text-[#2B6CB0] focus:ring-[#2B6CB0]">
                                    <span>
                                        <span class="font-medium text-gray-900">{{ $staff->user?->name }}</span>
                                        @if ($staff->position)
                                            <span class="block text-xs text-gray-500">{{ $staff->position }}</span>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('doctor.operations') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600">Cancel</a>
                    <button type="submit" class="rounded-lg bg-[#2B6CB0] px-4 py-2 text-sm font-semibold text-white hover:bg-[#1E4E82]">Submit Request</button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
