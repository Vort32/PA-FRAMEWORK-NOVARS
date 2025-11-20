@extends('layouts.admin')

@php($title = 'Add Patient')

@section('content')
    <x-card title="Create Patient">
        <form action="{{ route('admin.patients.store') }}" method="POST" class="space-y-6">
            @csrf
            @include('admin.patients._form', ['patient' => null])

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.patients.index') }}" class="glass-secondary px-4 py-2 text-sm font-medium">Cancel</a>
                <button type="submit" class="glass-primary px-5 py-2 text-sm">Save</button>
            </div>
        </form>
    </x-card>
@endsection
