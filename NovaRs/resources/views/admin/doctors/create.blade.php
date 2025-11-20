@extends('layouts.admin')

@php($title = 'Add Doctor')

@section('content')
    <x-card title="Create Doctor">
        <form action="{{ route('admin.doctors.store') }}" method="POST" class="space-y-6">
            @csrf
            @include('admin.doctors._form', ['doctor' => null])

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.doctors.index') }}" class="glass-secondary px-4 py-2 text-sm font-medium">Cancel</a>
                <button type="submit" class="glass-primary px-5 py-2 text-sm">Save</button>
            </div>
        </form>
    </x-card>
@endsection
