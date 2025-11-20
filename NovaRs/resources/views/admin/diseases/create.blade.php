@extends('layouts.admin')

@php($title = 'Add Disease')

@section('content')
    <x-card title="Create Disease">
        <form action="{{ route('admin.diseases.store') }}" method="POST" class="space-y-6">
            @csrf
            @include('admin.diseases._form', ['disease' => null])

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.diseases.index') }}" class="glass-secondary px-4 py-2 text-sm font-medium">Cancel</a>
                <button type="submit" class="glass-primary px-5 py-2 text-sm">Save</button>
            </div>
        </form>
    </x-card>
@endsection
