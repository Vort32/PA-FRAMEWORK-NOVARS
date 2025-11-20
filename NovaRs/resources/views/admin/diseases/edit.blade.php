@extends('layouts.admin')

@php($title = 'Edit Disease')

@section('content')
    <x-card title="Update Disease">
        <form action="{{ route('admin.diseases.update', $disease) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.diseases._form', ['disease' => $disease])

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.diseases.index') }}" class="glass-secondary px-4 py-2 text-sm font-medium">Cancel</a>
                <button type="submit" class="glass-primary px-5 py-2 text-sm">Update</button>
            </div>
        </form>
    </x-card>
@endsection
