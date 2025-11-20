@php
    $selectedEquipments = isset($operation) ? $operation->equipments->keyBy('id') : collect();
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div>
        @php
            $patientOptions = $patients->pluck('name', 'id')->toArray();
        @endphp
        <x-ui.select label="Patient" model="patient_id" :options="$patientOptions" :selected="old('patient_id', $operation->patient_id ?? null)" />
    </div>
    <div>
        @php
            $roomOptions = $rooms->filter(function ($room) {
                return in_array($room->status, [\App\Enums\RoomStatus::Available, \App\Enums\RoomStatus::Cleaning], true);
            })->mapWithKeys(function ($room) {
                $suffix = $room->status === \App\Enums\RoomStatus::Cleaning ? ' — Cleaning' : '';

                return [$room->id => $room->name . $suffix];
            })->toArray();
        @endphp
        <x-ui.select label="Room" model="room_id" :options="$roomOptions" :selected="old('room_id', $operation->room_id ?? null)" />
        <p class="mt-1 text-xs text-gray-500">Ruangan dengan status cleaning tidak dapat dipilih hingga staff menyelesaikan pembersihan.</p>
    </div>
    <div>
        @php
            $diseaseOptions = ['' => 'No disease selected'] + $diseases->pluck('name', 'id')->toArray();
        @endphp
        <x-ui.select label="Disease" model="disease_id" :options="$diseaseOptions" :selected="old('disease_id', $operation->disease_id ?? '')" />
    </div>
    <div>
        <label class="text-sm font-medium text-gray-700">Scheduled At</label>
        <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', isset($operation) ? $operation->scheduled_at->format('Y-m-d\\TH:i') : '') }}" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>
    <div>
        @php
            $statusOptions = collect($statuses)->mapWithKeys(function ($status) {
                return [$status->value => \Illuminate\Support\Str::headline($status->value)];
            })->toArray();
        @endphp
        <x-ui.select label="Status" model="status" :options="$statusOptions" :selected="old('status', $operation->status->value ?? '')" />
    </div>
    <div>
        <label class="text-sm font-medium text-gray-700">Estimated Duration (minutes)</label>
        <input type="number" name="estimated_duration_minutes" min="15" step="15" value="{{ old('estimated_duration_minutes', $operation->estimated_duration_minutes ?? 60) }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>
    <div class="md:col-span-2">
        <label class="text-sm font-medium text-gray-700">Notes</label>
        <textarea name="notes" rows="3" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">{{ old('notes', $operation->notes ?? '') }}</textarea>
    </div>

    <div class="md:col-span-2">
        <label class="text-sm font-medium text-gray-700">Equipments</label>
        <div class="mt-2 space-y-3">
            @foreach ($equipments as $equipment)
                @php($isChecked = in_array($equipment->id, old('equipment_ids', $selectedEquipments->keys()->all() ?? [])))
                @php($pivot = optional($selectedEquipments->get($equipment->id))->pivot)
                <div class="rounded-lg border border-gray-200 p-3">
                    <label class="flex items-start gap-3 text-sm text-gray-700">
                        <input type="checkbox" name="equipment_ids[]" value="{{ $equipment->id }}" {{ $isChecked ? 'checked' : '' }} class="mt-1 rounded border-gray-300 text-[#2B6CB0] focus:ring-[#38B2AC]">
                        <span>
                            <span class="font-semibold">{{ $equipment->name }}</span>
                            <span class="block text-xs text-gray-500">Available: {{ $equipment->quantity_available }}</span>
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
            @endforeach
        </div>
    </div>
</div>
