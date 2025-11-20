@extends('layouts.admin')

@php($title = 'Add Staff')

@section('content')
    <x-card title="Create Staff">
        <form action="{{ route('admin.staff.store') }}" method="POST" class="space-y-6">
            @csrf
            @include('admin.staff._form', ['staff' => null])

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.staff.index') }}" class="glass-secondary px-4 py-2 text-sm font-medium">Cancel</a>
                <button type="submit" class="glass-primary px-5 py-2 text-sm">Save</button>
            </div>
        </form>
    </x-card>
@endsection
