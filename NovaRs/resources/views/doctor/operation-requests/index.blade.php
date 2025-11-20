@extends('layouts.doctor')

@php
    $title = 'Patient Referrals';
    $statusOptions = ['' => 'All'] + collect($statuses)->mapWithKeys(fn ($status) => [$status->value => \Illuminate\Support\Str::headline($status->value)])->toArray();
@endphp

@section('content')
    <div class="flex flex-col gap-8">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <form method="GET" class="flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm">
                <div class="min-w-[180px]">
                    <x-ui.select label="Status" model="status" :options="$statusOptions" :selected="request('status')" />
                </div>
                <div>
                    <button type="submit" class="rounded-xl bg-[#2B6CB0] px-5 py-2 text-sm font-semibold text-white shadow hover:bg-[#1E4E82]">Filter</button>
                </div>
            </form>

            <p class="text-sm text-slate-500">Review incoming surgery referral requests submitted by patients.</p>
        </div>

        <x-card title="Referral Requests">
            <x-table :headers="['Submitted', 'Patient', 'Disease', 'Preferred Date', 'Status', 'Actions']">
                @forelse ($requests as $request)
                    <tr class="text-sm text-slate-700">
                        <td class="px-4 py-3 font-medium">{{ $request->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $request->patient?->name }}</td>
                        <td class="px-4 py-3">{{ $request->disease?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $request->preferred_date?->format('d M Y') ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-600">
                                {{ \Illuminate\Support\Str::headline($request->status->value) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('doctor.operation-requests.show', $request, false) }}" class="rounded-xl border border-[#2B6CB0] px-4 py-1.5 text-xs font-semibold text-[#2B6CB0] transition hover:bg-[#2B6CB0] hover:text-white">Review</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-sm text-slate-400">No referral requests found.</td>
                    </tr>
                @endforelse
                <tr>
                    <td colspan="6" class="px-4 py-4">
                        {{ $requests->links() }}
                    </td>
                </tr>
            </x-table>
        </x-card>
    </div>
@endsection
