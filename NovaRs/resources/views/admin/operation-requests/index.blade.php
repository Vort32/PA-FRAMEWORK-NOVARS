@php $isStaff = auth()->check() && auth()->user()->isRole(\App\Enums\UserRole::Staff); @endphp

@extends($isStaff ? 'layouts.staff' : 'layouts.admin')

@php $title = 'Patient Operation Requests'; @endphp

@section('content')
    <div class="flex flex-col gap-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <form method="GET" class="glass-panel flex flex-wrap items-center gap-3 px-4 py-4">
                @php
                    $statusOptions = ['' => 'All'] + collect($statuses)->mapWithKeys(function ($status) {
                        return [$status->value => \Illuminate\Support\Str::headline($status->value)];
                    })->toArray();
                @endphp
                <div class="min-w-[180px]">
                    <x-ui.select label="Status" model="status" :options="$statusOptions" :selected="request('status')" />
                </div>
                <div class="self-end">
                    <button type="submit" class="glass-primary px-5 py-2 text-sm">Filter</button>
                </div>
            </form>

            <p class="text-sm text-slate-300/80">Manage surgery requests submitted by patients.</p>
        </div>

        <x-table :headers="['Submitted', 'Patient', 'Doctor', 'Disease', 'Preferred Date', 'Referral Letter', 'Status', 'Actions']">
            @forelse ($requests as $request)
                <tr class="text-sm text-slate-200/90">
                    <td class="px-4 py-3">{{ $request->created_at->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3">{{ $request->patient?->name }}</td>
                    <td class="px-4 py-3">{{ $request->doctor?->name ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $request->disease?->name ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $request->preferred_date?->format('d M Y') ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if ($request->referral_letter_path)
                            <a href="{{ route('operation-requests.referral-letter', [$request], false) }}" target="_blank" class="glass-secondary inline-flex items-center gap-1.5 px-3 py-1 text-xs">
                                <i data-lucide="paperclip" class="h-3.5 w-3.5"></i>
                                View
                            </a>
                        @else
                            <span class="text-xs text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 capitalize">
                        <span class="glass-badge text-xs">{{ ucfirst($request->status->value) }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.operation-requests.show', $request, false) }}" class="glass-secondary px-4 py-1.5 text-xs font-semibold">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-4 text-center text-sm text-slate-400">No operation requests found.</td>
                </tr>
            @endforelse
            <tr>
                <td colspan="8" class="px-4 py-4">
                    {{ $requests->links() }}
                </td>
            </tr>
        </x-table>
    </div>
@endsection
