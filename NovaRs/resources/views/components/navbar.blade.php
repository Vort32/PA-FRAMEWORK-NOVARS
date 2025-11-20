@props([
    'title' => null,
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

    $navWrapper = [
        'patient' => 'relative mx-6 mb-6 mt-6 rounded-2xl border border-emerald-100/80 bg-white/70 shadow-lg backdrop-blur-md',
        'staff' => 'relative mx-6 mb-6 mt-6 rounded-2xl border border-emerald-100/70 bg-white/75 shadow-lg backdrop-blur-md dark:border-emerald-300/20 dark:bg-emerald-900/40',
        'doctor' => 'relative border-b border-slate-200 bg-white shadow-md dark:border-slate-700 dark:bg-slate-900',
        'admin' => 'relative border-b border-teal-100/70 bg-white/60 shadow-lg backdrop-blur-xl dark:border-slate-700 dark:bg-slate-800/70',
        'default' => 'bg-white shadow-sm',
    ][$context];

    $containerClasses = [
        'patient' => 'relative flex flex-col gap-4 px-6 py-6 md:flex-row md:items-center md:justify-between',
        'staff' => 'relative flex flex-col gap-4 px-6 py-6 md:flex-row md:items-center md:justify-between',
        'doctor' => 'relative mx-auto flex max-w-7xl items-center justify-between px-6 py-5',
        'admin' => 'relative mx-auto flex max-w-7xl items-center justify-between px-6 py-5',
        'default' => 'relative mx-auto flex max-w-7xl items-center justify-between px-6 py-4',
    ][$context];

    $titleColor = [
        'patient' => 'text-emerald-700 dark:text-emerald-100',
        'staff' => 'text-emerald-700 dark:text-emerald-100',
        'doctor' => 'text-slate-900 dark:text-white',
        'admin' => 'text-slate-900 dark:text-white',
        'default' => 'text-[#2B6CB0]',
    ][$context];

    $subtitleColor = [
        'patient' => 'text-emerald-500 dark:text-emerald-200/80',
        'staff' => 'text-emerald-500 dark:text-emerald-200/80',
        'doctor' => 'text-slate-500 dark:text-slate-400',
        'admin' => 'text-slate-500 dark:text-slate-300',
        'default' => 'text-gray-500',
    ][$context];

    $profileColor = [
        'patient' => 'text-emerald-700 dark:text-emerald-100',
        'staff' => 'text-emerald-700 dark:text-emerald-100',
        'doctor' => 'text-slate-900 dark:text-slate-100',
        'admin' => 'text-slate-900 dark:text-white',
        'default' => 'text-gray-700',
    ][$context];

    $roleColor = [
        'patient' => 'text-emerald-500 dark:text-emerald-200/80',
        'staff' => 'text-emerald-500 dark:text-emerald-200/80',
        'doctor' => 'text-slate-500 dark:text-slate-400',
        'admin' => 'text-slate-500 dark:text-slate-300',
        'default' => 'text-gray-400',
    ][$context];

    $logoutButton = [
        'patient' => 'inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white shadow-md transition hover:bg-emerald-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500',
        'staff' => 'inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white shadow-md transition hover:bg-emerald-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500',
        'doctor' => 'inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-md transition hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600',
        'admin' => 'inline-flex items-center gap-2 rounded-xl bg-teal-600 px-4 py-2 text-sm font-semibold text-white shadow-md transition hover:bg-teal-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-500',
        'default' => 'rounded-md bg-[#38B2AC] px-4 py-2 text-sm font-semibold text-white shadow hover:bg-[#2B6CB0] transition',
    ][$context];
@endphp

<nav class="{{ $navWrapper }}">
    <div class="{{ $containerClasses }}">
        <div>
            <h1 class="text-lg font-semibold {{ $titleColor }}">{{ $title }}</h1>
            <p class="text-sm {{ $subtitleColor }}">{{ now()->format('l, d F Y') }}</p>
        </div>
        <div class="flex items-center gap-4">
            @auth
                <div class="text-right {{ $profileColor }}">
                    <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                    <p class="text-xs capitalize {{ $roleColor }}">{{ auth()->user()->role->value ?? auth()->user()->role }}</p>
                </div>
                <form action="{{ route('logout', [], false) }}" method="POST">
                    @csrf
                    <button type="submit" class="{{ $logoutButton }}">
                        <i data-lucide="log-out" class="h-4 w-4"></i>
                        Logout
                    </button>
                </form>
            @endauth
        </div>
    </div>
</nav>
