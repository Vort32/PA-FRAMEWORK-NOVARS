@extends('layouts.app', [
    'bodyClass' => 'bg-gradient-to-br from-teal-50 to-slate-50 text-slate-900 dark:from-slate-900 dark:to-gray-800 dark:text-slate-100',
    'outerWrapperClass' => 'min-h-screen flex bg-transparent',
    'contentWrapperClass' => 'flex-1 flex flex-col min-h-screen',
    'mainClass' => 'p-10 md:p-12 space-y-10'
])

@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
        ['label' => 'Patients', 'route' => 'admin.patients.index'],
        ['label' => 'Doctors', 'route' => 'admin.doctors.index'],
        ['label' => 'Staff', 'route' => 'admin.staff.index'],
        ['label' => 'Rooms', 'route' => 'admin.rooms.index'],
        ['label' => 'Equipments', 'route' => 'admin.equipments.index'],
        ['label' => 'Diseases', 'route' => 'admin.diseases.index'],
        ['label' => 'Operations', 'route' => 'admin.operations.index'],
    ];
@endphp

@section('sidebar')
    @include('components.sidebar', ['items' => $navItems, 'brand' => 'Admin'])
@endsection

@section('navbar')
    @include('components.navbar', ['title' => $title ?? 'Admin Panel'])
@endsection
