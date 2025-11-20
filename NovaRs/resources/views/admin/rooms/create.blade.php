@extends('layouts.admin')

@php($title = 'Add Room')

@section('content')
    <x-card title="Create Operating Room">
        <form action="{{ route('admin.rooms.store') }}" method="POST" class="space-y-6">
            @csrf
            @include('admin.rooms._form', ['room' => null])

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.rooms.index') }}" class="glass-secondary px-4 py-2 text-sm font-medium">Cancel</a>
                <button type="submit" class="glass-primary px-5 py-2 text-sm">Save</button>
            </div>
        </form>
    </x-card>
@endsection
