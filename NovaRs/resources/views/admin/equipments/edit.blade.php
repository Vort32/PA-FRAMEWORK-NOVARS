@extends('layouts.admin')

@php($title = 'Edit Equipment')

@section('content')
    <x-card title="Update Equipment">
        <form action="{{ route('admin.equipments.update', $equipment) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.equipments._form', ['equipment' => $equipment])

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.equipments.index') }}" class="glass-secondary px-4 py-2 text-sm font-medium">Cancel</a>
                <button type="submit" class="glass-primary px-5 py-2 text-sm">Update</button>
            </div>
        </form>
    </x-card>
@endsection
