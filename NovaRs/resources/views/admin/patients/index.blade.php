@extends('layouts.admin')

@php($title = 'Patients Management')

@section('content')
    <div class="flex flex-col gap-8">
        <div class="flex flex-wrap items-center justify-end gap-3">
            <a href="{{ route('admin.patients.export', ['format' => 'xlsx']) }}" class="inline-flex items-center glass-secondary px-5 py-2 text-sm font-semibold">Export Excel</a>
            <a href="{{ route('admin.patients.export', ['format' => 'pdf']) }}" class="inline-flex items-center glass-primary px-5 py-2 text-sm font-semibold">Export PDF</a>
        </div>

        <x-table :headers="['MRN', 'Name', 'Email', 'Phone', 'Actions']">
            @forelse ($patients as $patient)
                <tr class="text-sm text-slate-200/90">
                    <td class="px-4 py-3">{{ $patient->user->medical_record_number }}</td>
                    <td class="px-4 py-3">{{ $patient->user->name }}</td>
                    <td class="px-4 py-3">{{ $patient->user->email }}</td>
                    <td class="px-4 py-3">{{ $patient->user->phone }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.patients.edit', $patient) }}" class="glass-secondary px-3 py-1 text-xs font-semibold">Edit</a>
                            <form action="{{ route('admin.patients.destroy', $patient) }}" method="POST" onsubmit="return confirm('Delete this patient?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-full border border-red-400/70 bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-200">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-4 text-center text-sm text-slate-400">No patients found.</td>
                </tr>
            @endforelse
            <tr>
                <td colspan="5" class="px-4 py-4">
                    {{ $patients->links() }}
                </td>
            </tr>
        </x-table>
    </div>
@endsection
