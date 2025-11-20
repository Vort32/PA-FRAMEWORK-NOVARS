@extends('layouts.admin')

@php($title = 'Doctors Directory')

@section('content')
    <div class="flex flex-col gap-8">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.doctors.create') }}" class="inline-flex items-center glass-primary px-5 py-2 text-sm">Add Doctor</a>
        </div>

        <x-table :headers="['Name', 'Email', 'Specialization', 'License', 'Experience', 'Actions']">
            @forelse ($doctors as $doctor)
                <tr class="text-sm text-slate-200/90">
                    <td class="px-4 py-3">{{ $doctor->user->name }}</td>
                    <td class="px-4 py-3">{{ $doctor->user->email }}</td>
                    <td class="px-4 py-3">{{ $doctor->specialization }}</td>
                    <td class="px-4 py-3">{{ $doctor->license_number }}</td>
                    <td class="px-4 py-3">{{ $doctor->years_of_experience }} yrs</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.doctors.edit', $doctor) }}" class="glass-secondary px-3 py-1 text-xs font-semibold">Edit</a>
                            <form action="{{ route('admin.doctors.destroy', $doctor) }}" method="POST" onsubmit="return confirm('Delete this doctor?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-full border border-red-400/70 bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-200">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-4 text-center text-sm text-slate-400">No doctors found.</td>
                </tr>
            @endforelse
            <tr>
                <td colspan="6" class="px-4 py-4">
                    {{ $doctors->links() }}
                </td>
            </tr>
        </x-table>
    </div>
@endsection
