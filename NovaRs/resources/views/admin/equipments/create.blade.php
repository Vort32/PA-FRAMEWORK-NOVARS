@extends('layouts.admin')

@php($title = 'Add Equipment')

@section('content')
    <x-card title="Create Equipment">
        <form action="{{ route('admin.equipments.store') }}" method="POST" class="space-y-6">
            @csrf
            @include('admin.equipments._form', ['equipment' => null])

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.equipments.index') }}" class="glass-secondary px-4 py-2 text-sm font-medium">Cancel</a>
                <button type="submit" class="glass-primary px-5 py-2 text-sm">Save</button>
            </div>
        </form>
    </x-card>
@endsection
