@php
    $selectedEquipments = isset($room) ? $room->equipments->keyBy('id') : collect();
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-sm font-medium text-gray-700">Room Name</label>
        <input type="text" name="name" value="{{ old('name', $room->name ?? '') }}" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>
    <div>
        <label class="text-sm font-medium text-gray-700">Room Code</label>
        <input type="text" name="code" value="{{ old('code', $room->code ?? '') }}" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>
    <div>
        @php
            $statusOptions = collect($statuses)->mapWithKeys(function ($status) {
                return [$status->value => \Illuminate\Support\Str::headline($status->value)];
            })->toArray();
        @endphp
        <x-ui.select label="Status" model="status" :options="$statusOptions" :selected="old('status', $room->status->value ?? '')" />
    </div>
    <div>
        <label class="text-sm font-medium text-gray-700">Capacity</label>
        <input type="number" min="1" name="capacity" value="{{ old('capacity', $room->capacity ?? 1) }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>
    <div class="md:col-span-2">
        <label class="text-sm font-medium text-gray-700">Notes</label>
        <textarea name="notes" rows="3" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">{{ old('notes', $room->notes ?? '') }}</textarea>
    </div>

    <div class="md:col-span-2">
        <label class="text-sm font-medium text-gray-700">Equipments</label>
        <div class="mt-2 grid gap-3 md:grid-cols-2">
            @foreach ($equipments as $equipment)
                @php($isChecked = in_array($equipment->id, old('equipment_ids', $selectedEquipments->keys()->all() ?? [])))
                <div class="flex items-center justify-between gap-2 rounded-lg border border-gray-200 px-3 py-2">
                    <label class="flex items-center gap-3 text-sm text-gray-700">
                        <input type="checkbox" name="equipment_ids[]" value="{{ $equipment->id }}" {{ $isChecked ? 'checked' : '' }} class="rounded border-gray-300 text-[#2B6CB0] focus:ring-[#38B2AC]">
                        <span>
                            <span class="font-medium">{{ $equipment->name }}</span>
                            <span class="block text-xs text-gray-500">Available: {{ $equipment->quantity_available }}</span>
                        </span>
                    </label>
                    <input type="number" min="1" name="equipment_quantities[{{ $equipment->id }}]" value="{{ old('equipment_quantities.'.$equipment->id, $selectedEquipments[$equipment->id]->pivot->quantity ?? 1) }}" class="w-20 rounded border border-gray-200 px-2 py-1 text-sm focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
                </div>
            @endforeach
        </div>
    </div>
</div>
