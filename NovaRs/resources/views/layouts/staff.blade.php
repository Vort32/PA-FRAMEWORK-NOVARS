@extends('layouts.app', [
    'bodyClass' => 'bg-gradient-to-br from-emerald-50 to-white text-emerald-900 dark:from-slate-900 dark:to-emerald-950 dark:text-emerald-50',
    'outerWrapperClass' => 'min-h-screen flex bg-transparent',
    'contentWrapperClass' => 'flex-1 flex flex-col min-h-screen',
    'mainClass' => 'p-8 md:p-10 space-y-8'
])

@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'staff.dashboard'],
        ['label' => 'Rooms', 'href' => route('staff.dashboard') . '#rooms'],
    ];
@endphp

@section('sidebar')
    @include('components.sidebar', ['items' => $navItems, 'brand' => 'Staff'])
@endsection

@section('navbar')
    @include('components.navbar', ['title' => $title ?? 'Staff Console'])
@endsection
