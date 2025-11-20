@extends('layouts.admin')

@php($title = 'Edit Room')

@section('content')
    <x-card title="Update Operating Room">
        <form action="{{ route('admin.rooms.update', $room) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.rooms._form', ['room' => $room])

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.rooms.index') }}" class="glass-secondary px-4 py-2 text-sm font-medium">Cancel</a>
                <button type="submit" class="glass-primary px-5 py-2 text-sm">Update</button>
            </div>
        </form>
    </x-card>
@endsection
