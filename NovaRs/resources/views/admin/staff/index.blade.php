@extends('layouts.admin')

@php($title = 'Staff Management')

@section('content')
    <div class="flex flex-col gap-8">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.staff.create') }}" class="inline-flex items-center glass-primary px-5 py-2 text-sm">Add Staff</a>
        </div>

        <x-table :headers="['Name', 'Email', 'Position', 'Shift', 'Actions']">
            @forelse ($staff as $member)
                <tr class="text-sm text-slate-200/90">
                    <td class="px-4 py-3">{{ $member->user->name }}</td>
                    <td class="px-4 py-3">{{ $member->user->email }}</td>
                    <td class="px-4 py-3">{{ $member->position }}</td>
                    <td class="px-4 py-3 capitalize">{{ $member->shift_type }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.staff.edit', $member) }}" class="glass-secondary px-3 py-1 text-xs font-semibold">Edit</a>
                            <form action="{{ route('admin.staff.destroy', $member) }}" method="POST" onsubmit="return confirm('Delete this staff member?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-full border border-red-400/70 bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-200">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-4 text-center text-sm text-slate-400">No staff members found.</td>
                </tr>
            @endforelse
            <tr>
                <td colspan="5" class="px-4 py-4">
                    {{ $staff->links() }}
                </td>
            </tr>
        </x-table>
    </div>
@endsection
