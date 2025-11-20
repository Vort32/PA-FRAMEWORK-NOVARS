@extends('layouts.admin')

@php($title = 'Edit Patient')

@section('content')
    <x-card title="Update Patient">
        <form action="{{ route('admin.patients.update', $patient) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.patients._form', ['patient' => $patient])

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.patients.index') }}" class="glass-secondary px-4 py-2 text-sm font-medium">Cancel</a>
                <button type="submit" class="glass-primary px-5 py-2 text-sm">Update</button>
            </div>
        </form>
    </x-card>
@endsection
