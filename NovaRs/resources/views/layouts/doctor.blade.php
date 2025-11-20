@extends('layouts.app', [
    'bodyClass' => 'bg-slate-50 text-slate-900 dark:bg-slate-900 dark:text-slate-100',
    'outerWrapperClass' => 'min-h-screen flex bg-transparent',
    'contentWrapperClass' => 'flex-1 flex flex-col min-h-screen',
    'mainClass' => 'p-8 md:p-10 space-y-8'
])

@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'doctor.dashboard'],
        ['label' => 'My Operations', 'route' => 'doctor.operations'],
        ['label' => 'Reports', 'route' => 'doctor.reports'],
    ];
@endphp

@section('sidebar')
    @include('components.sidebar', ['items' => $navItems, 'brand' => 'Doctor'])
@endsection

@section('navbar')
    @include('components.navbar', ['title' => $title ?? 'Doctor Workspace'])
@endsection
