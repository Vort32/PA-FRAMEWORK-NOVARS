@extends('layouts.app', [
    'bodyClass' => 'bg-gradient-to-br from-emerald-50 to-white dark:from-gray-900 dark:to-gray-800 text-emerald-900 dark:text-emerald-100',
    'outerWrapperClass' => 'min-h-screen flex bg-transparent',
    'contentWrapperClass' => 'flex-1 flex flex-col min-h-screen',
    'mainClass' => 'p-8 md:p-10 space-y-8'
])

@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'patient.dashboard'],
        ['label' => 'My Reports', 'href' => route('patient.operations')],
    ];
@endphp

@section('sidebar')
    @include('components.sidebar', ['items' => $navItems, 'brand' => 'Patient'])
@endsection

@section('navbar')
    @include('components.navbar', ['title' => $title ?? 'Patient Portal'])
@endsection
