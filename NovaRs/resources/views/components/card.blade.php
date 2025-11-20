@props([
    'title' => null,
    'actions' => null,
])

@php
    $routeName = request()->route()?->getName();
    $context = 'default';

    if ($routeName) {
        if (\Illuminate\Support\Str::startsWith($routeName, 'admin.')) {
            $context = 'admin';
        } elseif (\Illuminate\Support\Str::startsWith($routeName, 'doctor.')) {
            $context = 'doctor';
        } elseif (\Illuminate\Support\Str::startsWith($routeName, 'patient.')) {
            $context = 'patient';
        } elseif (\Illuminate\Support\Str::startsWith($routeName, 'staff.')) {
            $context = 'staff';
        }
    }

    $baseClasses = [
        'patient' => 'rounded-2xl border border-emerald-100/80 bg-white/70 text-emerald-900 shadow-md backdrop-blur-sm',
        'staff' => 'rounded-2xl border border-emerald-100/70 bg-white/75 text-emerald-900 shadow-md backdrop-blur-sm dark:border-emerald-300/20 dark:bg-emerald-900/40 dark:text-emerald-50',
        'doctor' => 'rounded-xl border border-slate-200 bg-white text-slate-900 shadow-lg dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100',
        'admin' => 'rounded-2xl border border-teal-200 bg-white text-slate-900 shadow-xl dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100',
        'default' => 'rounded-2xl border border-gray-100 bg-white text-gray-800 shadow-sm',
    ][$context];

    $titleClasses = [
        'patient' => 'text-emerald-800 dark:text-emerald-50',
        'staff' => 'text-emerald-800 dark:text-emerald-50',
        'doctor' => 'text-slate-900 dark:text-slate-50',
        'admin' => 'text-slate-900 dark:text-white',
        'default' => 'text-gray-800',
    ][$context];
@endphp

<div {{ $attributes->class($baseClasses)->merge(['class' => 'p-6 sm:p-7 space-y-5 transition-shadow duration-200']) }}>
    @if ($title)
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-lg font-semibold {{ $titleClasses }}">{{ $title }}</h2>
            {{ $actions }}
        </div>
    @endif

    {{ $slot }}
</div>
