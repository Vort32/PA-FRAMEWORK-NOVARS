@extends('layouts.admin')

@php($title = 'Schedule Operation')

@section('content')
    <x-card title="Create Operation Schedule">
        <form action="{{ route('admin.operations.store') }}" method="POST" class="space-y-6">
            @csrf
            @include('admin.operations._form', ['operation' => null])

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.operations.index') }}" class="glass-secondary px-4 py-2 text-sm font-medium">Cancel</a>
                <button type="submit" class="glass-primary px-5 py-2 text-sm">Save</button>
            </div>
        </form>
    </x-card>
@endsection
