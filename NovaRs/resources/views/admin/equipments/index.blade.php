@extends('layouts.admin')

@php($title = 'Equipments Inventory')

@section('content')
    <div class="flex flex-col gap-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <a href="{{ route('admin.equipments.create') }}" class="inline-flex items-center glass-primary px-5 py-2 text-sm">Add Equipment</a>

            <form action="{{ route('admin.equipments.import') }}" method="POST" enctype="multipart/form-data" class="glass-panel flex items-center gap-3 px-4 py-3">
                @csrf
                <label class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400/80">Import Equipments</label>
                <input type="file" name="file" required accept=".xlsx,.csv" class="text-sm text-slate-200/90">
                <button type="submit" class="glass-secondary px-4 py-1.5 text-sm font-semibold">Upload</button>
            </form>
        </div>

        <x-table :headers="['Name', 'Category', 'Serial Number', 'Quantity', 'Actions']">
            @forelse ($equipments as $equipment)
                <tr class="text-sm text-slate-200/90">
                    <td class="px-4 py-3">{{ $equipment->name }}</td>
                    <td class="px-4 py-3">{{ $equipment->category }}</td>
                    <td class="px-4 py-3">{{ $equipment->serial_number }}</td>
                    <td class="px-4 py-3">{{ $equipment->quantity_available }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.equipments.edit', $equipment) }}" class="glass-secondary px-3 py-1 text-xs font-semibold">Edit</a>
                            <form action="{{ route('admin.equipments.destroy', $equipment) }}" method="POST" onsubmit="return confirm('Delete this equipment?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-full border border-red-400/70 bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-200">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-4 text-center text-sm text-slate-400">No equipments found.</td>
                </tr>
            @endforelse
            <tr>
                <td colspan="5" class="px-4 py-4">
                    {{ $equipments->links() }}
                </td>
            </tr>
        </x-table>
    </div>
@endsection
