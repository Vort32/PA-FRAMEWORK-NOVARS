@extends('layouts.staff')

@php
    $title = 'Staff Dashboard';
    $totalRooms = $rooms->count();
    $availableRooms = $rooms->filter(function ($room) {
        return $room->status === \App\Enums\RoomStatus::Available;
    })->count();
    $inUseRooms = $rooms->filter(function ($room) {
        return $room->status === \App\Enums\RoomStatus::InUse;
    })->count();
    $cleaningRooms = $rooms->filter(function ($room) {
        return $room->status === \App\Enums\RoomStatus::Cleaning;
    })->count();
@endphp

@section('content')
    <div class="space-y-8">
        <section class="relative overflow-hidden rounded-3xl border border-emerald-100/80 bg-white/75 p-8 shadow-lg backdrop-blur-sm dark:border-emerald-900/30 dark:bg-emerald-950/30">
            <div class="pointer-events-none absolute -right-16 -top-10 h-48 w-48 rounded-full bg-emerald-200/50 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-14 left-10 h-40 w-40 rounded-full bg-emerald-100/60 blur-3xl"></div>

            <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div class="space-y-3">
                    <p class="inline-flex items-center gap-2 rounded-full border border-emerald-200/70 bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.32em] text-emerald-600">
                        <i data-lucide="sprout" class="h-4 w-4"></i>
                        Room care
                    </p>
                    <h1 class="text-3xl font-semibold text-emerald-900 dark:text-emerald-100">Kelola ketersediaan ruangan dengan nyaman</h1>
                    <p class="max-w-2xl text-sm text-emerald-700/80 dark:text-emerald-200/80">Fokus memastikan setiap ruangan siap pakai, peralatan lengkap, dan status selalu terbarui tanpa tampilan yang melelahkan mata.</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-emerald-200/80 bg-white px-5 py-4 text-center shadow-sm dark:border-emerald-800/40 dark:bg-emerald-950/40">
                        <p class="text-xs font-medium uppercase tracking-wide text-emerald-500">Total ruang</p>
                        <p class="mt-2 text-2xl font-semibold text-emerald-800 dark:text-emerald-100">{{ $totalRooms }}</p>
                    </div>
                    <div class="rounded-2xl border border-emerald-200/80 bg-white px-5 py-4 text-center shadow-sm dark:border-emerald-800/40 dark:bg-emerald-950/40">
                        <p class="text-xs font-medium uppercase tracking-wide text-emerald-500">Tersedia</p>
                        <p class="mt-2 text-2xl font-semibold text-emerald-800 dark:text-emerald-100">{{ $availableRooms }}</p>
                    </div>
                    <div class="rounded-2xl border border-emerald-200/80 bg-white px-5 py-4 text-center shadow-sm dark:border-emerald-800/40 dark:bg-emerald-950/40">
                        <p class="text-xs font-medium uppercase tracking-wide text-emerald-500">Cleaning</p>
                        <p class="mt-2 text-2xl font-semibold text-emerald-800 dark:text-emerald-100">{{ $cleaningRooms }}</p>
                    </div>
                </div>
            </div>
        </section>

        <x-card title="Status ruang" id="rooms">
            <div class="space-y-4">
                @foreach ($rooms as $room)
                    @php
                        $roomStatus = \Illuminate\Support\Str::headline($room->status->value);
                        $badgeClasses = match ($room->status) {
                            \App\Enums\RoomStatus::Available => 'bg-green-100 text-green-600 border border-green-200',
                            \App\Enums\RoomStatus::InUse => 'bg-yellow-100 text-yellow-600 border border-yellow-200',
                            \App\Enums\RoomStatus::Cleaning => 'bg-blue-100 text-blue-600 border border-blue-200',
                        };
                    @endphp
                    <div class="rounded-2xl border border-emerald-100/80 bg-white/85 p-4 shadow-sm transition hover:border-emerald-200 hover:shadow-md dark:border-emerald-800/40 dark:bg-emerald-950/30 dark:hover:border-emerald-700/50">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-100">{{ $room->name }} ({{ $room->code }})</p>
                                <p class="text-xs text-emerald-600/80 dark:text-emerald-200/80">Kapasitas: {{ $room->capacity }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClasses }}">
                                    <i data-lucide="activity-square" class="h-3.5 w-3.5"></i>
                                    {{ $roomStatus }}
                                </span>
                                <form action="{{ route('rooms.status', $room) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    @php
                                        $roomStatusOptions = collect(\App\Enums\RoomStatus::cases())->mapWithKeys(function ($status) {
                                            return [$status->value => \Illuminate\Support\Str::headline($status->value)];
                                        })->toArray();
                                    @endphp
                                    <x-ui.select model="status" :options="$roomStatusOptions" :selected="$room->status->value" :auto-submit="true" class="min-w-[160px]" />
                                </form>
                            </div>
                        </div>
                        <ul class="mt-3 grid gap-1 text-xs text-emerald-700/80 dark:text-emerald-200/80">
                            @foreach ($room->equipments as $equipment)
                                <li class="flex items-center justify-between">
                                    <span>{{ $equipment->name }}</span>
                                    <span class="text-emerald-500/80 dark:text-emerald-300/80">×{{ $equipment->pivot->quantity }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </x-card>

        <x-card title="Ringkasan ruangan">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-emerald-200/80 bg-white px-4 py-4 text-sm shadow-sm dark:border-emerald-800/40 dark:bg-emerald-950/30">
                    <p class="text-emerald-500/90">Sedang digunakan</p>
                    <p class="mt-2 text-2xl font-semibold text-emerald-800 dark:text-emerald-100">{{ $inUseRooms }}</p>
                    <p class="text-xs text-emerald-600/80 dark:text-emerald-200/70">Pastikan koordinasi jadwal antar tim berjalan lancar.</p>
                </div>
                <div class="rounded-2xl border border-emerald-200/80 bg-white px-4 py-4 text-sm shadow-sm dark:border-emerald-800/40 dark:bg-emerald-950/30">
                    <p class="text-emerald-500/90">Siap dipakai</p>
                    <p class="mt-2 text-2xl font-semibold text-emerald-800 dark:text-emerald-100">{{ $availableRooms }}</p>
                    <p class="text-xs text-emerald-600/80 dark:text-emerald-200/70">Gunakan kembali untuk penjadwalan operasi baru.</p>
                </div>
                <div class="rounded-2xl border border-emerald-200/80 bg-white px-4 py-4 text-sm shadow-sm dark:border-emerald-800/40 dark:bg-emerald-950/30">
                    <p class="text-emerald-500/90">Dalam pembersihan</p>
                    <p class="mt-2 text-2xl font-semibold text-emerald-800 dark:text-emerald-100">{{ $cleaningRooms }}</p>
                    <p class="text-xs text-emerald-600/80 dark:text-emerald-200/70">Tunggu konfirmasi selesai cleaning sebelum dijadwalkan.</p>
                </div>
            </div>
        </x-card>
    </div>
@endsection
