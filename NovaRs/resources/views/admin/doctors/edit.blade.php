@extends('layouts.admin')

@php($title = 'Edit Doctor')

@section('content')
    <x-card title="Update Doctor">
        <form action="{{ route('admin.doctors.update', $doctor) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.doctors._form', ['doctor' => $doctor])

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.doctors.index') }}" class="glass-secondary px-4 py-2 text-sm font-medium">Cancel</a>
                <button type="submit" class="glass-primary px-5 py-2 text-sm">Update</button>
            </div>
        </form>
    </x-card>
@endsection
