@extends('layouts.admin')

@php($title = 'Diseases Catalog')

@section('content')
    <div class="flex flex-col gap-8">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.diseases.create') }}" class="inline-flex items-center glass-primary px-5 py-2 text-sm">Add Disease</a>
        </div>

        <x-table :headers="['Name', 'ICD Code', 'Description', 'Actions']">
            @forelse ($diseases as $disease)
                <tr class="text-sm text-slate-200/90">
                    <td class="px-4 py-3">{{ $disease->name }}</td>
                    <td class="px-4 py-3">{{ $disease->icd_code }}</td>
                    <td class="px-4 py-3">{{ Str::limit($disease->description, 80) }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.diseases.edit', $disease) }}" class="glass-secondary px-3 py-1 text-xs font-semibold">Edit</a>
                            <form action="{{ route('admin.diseases.destroy', $disease) }}" method="POST" onsubmit="return confirm('Delete this disease record?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-full border border-red-400/70 bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-200">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-4 text-center text-sm text-slate-400">No disease records found.</td>
                </tr>
            @endforelse
            <tr>
                <td colspan="4" class="px-4 py-4">
                    {{ $diseases->links() }}
                </td>
            </tr>
        </x-table>
    </div>
@endsection
