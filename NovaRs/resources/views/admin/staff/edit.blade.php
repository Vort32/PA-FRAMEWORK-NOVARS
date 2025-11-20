@extends('layouts.admin')

@php($title = 'Edit Staff')

@section('content')
    <x-card title="Update Staff">
        <form action="{{ route('admin.staff.update', $staff) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.staff._form', ['staff' => $staff])

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.staff.index') }}" class="glass-secondary px-4 py-2 text-sm font-medium">Cancel</a>
                <button type="submit" class="glass-primary px-5 py-2 text-sm">Update</button>
            </div>
        </form>
    </x-card>
@endsection
