@extends('layouts.admin')

@php($title = 'Edit Operation')

@section('content')
    <x-card title="Update Operation Schedule">
        <form action="{{ route('admin.operations.update', $operation) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.operations._form', ['operation' => $operation])

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.operations.index') }}" class="glass-secondary px-4 py-2 text-sm font-medium">Cancel</a>
                <button type="submit" class="glass-primary px-5 py-2 text-sm">Update</button>
            </div>
        </form>
    </x-card>
@endsection
