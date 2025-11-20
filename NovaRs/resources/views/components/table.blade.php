@props([
    'headers' => [],
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

    $wrapperClasses = [
        'patient' => 'overflow-x-auto rounded-2xl border border-emerald-100/80 bg-white/80 shadow-md backdrop-blur-sm',
        'staff' => 'overflow-x-auto rounded-2xl border border-emerald-100/70 bg-white/85 shadow-md backdrop-blur-sm dark:border-emerald-300/20 dark:bg-emerald-900/40',
        'doctor' => 'overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-lg dark:border-slate-700 dark:bg-slate-900',
        'admin' => 'overflow-x-auto rounded-2xl border border-teal-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-800',
        'default' => 'overflow-x-auto rounded-xl border border-gray-100 bg-white shadow-sm',
    ][$context];

    $tableDivider = [
        'patient' => 'divide-y divide-emerald-100/70',
        'staff' => 'divide-y divide-emerald-100/70 dark:divide-emerald-300/20',
        'doctor' => 'divide-y divide-slate-100 dark:divide-slate-700',
        'admin' => 'divide-y divide-slate-100 dark:divide-slate-700',
        'default' => 'divide-y divide-gray-200',
    ][$context];

    $theadClasses = [
        'patient' => 'bg-emerald-500/90 text-emerald-50',
        'staff' => 'bg-emerald-500/90 text-emerald-50',
        'doctor' => 'bg-slate-900 text-slate-100',
        'admin' => 'bg-teal-600 text-white',
        'default' => 'bg-[#2B6CB0] text-white',
    ][$context];

    $theadCellClasses = [
        'patient' => 'text-xs font-semibold uppercase tracking-wide text-emerald-50',
        'staff' => 'text-xs font-semibold uppercase tracking-wide text-emerald-50',
        'doctor' => 'text-xs font-semibold uppercase tracking-[0.24em] text-slate-100',
        'admin' => 'text-xs font-semibold uppercase tracking-[0.18em] text-white/95',
        'default' => 'text-xs font-semibold uppercase tracking-wide text-white',
    ][$context];

    $tbodyClasses = [
        'patient' => 'bg-white/80 text-emerald-900',
        'staff' => 'bg-white/85 text-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-50',
        'doctor' => 'bg-white text-slate-900 dark:bg-slate-900 dark:text-slate-100',
        'admin' => 'bg-white text-slate-900 dark:bg-slate-800 dark:text-slate-100',
        'default' => 'bg-white text-gray-800',
    ][$context];
@endphp

<div class="{{ $wrapperClasses }}">
    <table class="min-w-full {{ $tableDivider }}">
        <thead class="{{ $theadClasses }}">
            <tr>
                @foreach ($headers as $header)
                    <th scope="col" class="px-4 py-3 text-left {{ $theadCellClasses }}">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="{{ $tbodyClasses }}">
            {{ $slot }}
        </tbody>
    </table>
</div>
