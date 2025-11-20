@extends('layouts.admin')

@php($title = 'Operating Rooms')

@section('content')
    <div class="flex flex-col gap-8">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.rooms.create') }}" class="inline-flex items-center glass-primary px-5 py-2 text-sm">Add Room</a>
        </div>

        <x-table :headers="['Code', 'Name', 'Status', 'Capacity', 'Equipments', 'Actions']">
            @forelse ($rooms as $room)
                <tr class="text-sm text-slate-200/90">
                    <td class="px-4 py-3 font-medium text-slate-100">{{ $room->code }}</td>
                    <td class="px-4 py-3">{{ $room->name }}</td>
                    <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $room->status->value ?? $room->status) }}</td>
                    <td class="px-4 py-3">{{ $room->capacity }}</td>
                    <td class="px-4 py-3">
                        <ul class="space-y-1 text-xs text-slate-300/80">
                            @foreach ($room->equipments as $equipment)
                                <li>{{ $equipment->name }} <span class="text-slate-400/80">×{{ $equipment->pivot->quantity }}</span></li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.rooms.edit', $room) }}" class="glass-secondary px-3 py-1 text-xs font-semibold">Edit</a>
                            <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" onsubmit="return confirm('Delete this room?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-full border border-red-400/70 bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-200">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-4 text-center text-sm text-slate-400">No rooms found.</td>
                </tr>
            @endforelse
            <tr>
                <td colspan="6" class="px-4 py-4">
                    {{ $rooms->links() }}
                </td>
            </tr>
        </x-table>
    </div>
@endsection
