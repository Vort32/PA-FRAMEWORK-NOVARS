@extends(auth()->check() && auth()->user()->isRole(\App\Enums\UserRole::Staff) ? 'layouts.staff' : 'layouts.admin')

@php
    $isStaff = auth()->check() && auth()->user()->isRole(\App\Enums\UserRole::Staff);
    $title = $isStaff ? 'Operations Monitoring' : 'Operations Management';
    $filters = request()->only(['status', 'from', 'to', 'doctor_id', 'room_id']);
@endphp

@section('content')
    <div class="flex flex-col gap-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <form method="GET" class="glass-panel flex flex-wrap items-center gap-4 px-5 py-5">
                @php
                    $statusFilterOptions = ['' => 'All'] + collect($statuses)->mapWithKeys(function ($status) {
                        return [$status->value => \Illuminate\Support\Str::headline($status->value)];
                    })->toArray();
                    $doctorFilterOptions = ['' => 'All'] + $doctors->pluck('name', 'id')->toArray();
                    $roomFilterOptions = ['' => 'All'] + $rooms->pluck('name', 'id')->toArray();
                @endphp

                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400/80">From</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="mt-2 rounded-full border border-white/20 bg-white/5 px-3 py-1.5 text-sm text-slate-100 focus:border-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400/80">To</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="mt-2 rounded-full border border-white/20 bg-white/5 px-3 py-1.5 text-sm text-slate-100 focus:border-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                </div>
                <div class="min-w-[180px]">
                    <x-ui.select label="Status" model="status" :options="$statusFilterOptions" :selected="request('status')" />
                </div>
                <div class="min-w-[200px]">
                    <x-ui.select label="Doctor" model="doctor_id" :options="$doctorFilterOptions" :selected="request('doctor_id')" />
                </div>
                <div class="min-w-[200px]">
                    <x-ui.select label="Room" model="room_id" :options="$roomFilterOptions" :selected="request('room_id')" />
                </div>
                <div class="self-end">
                    <button type="submit" class="glass-primary px-5 py-2 text-sm">Filter</button>
                </div>
            </form>

            <div class="flex flex-wrap items-center gap-3">
                @can('access-admin')
                    <a href="{{ route('admin.operations.create') }}" class="glass-primary px-5 py-2 text-sm">Schedule Operation</a>

                    <a href="{{ route('admin.operations.export', array_merge($filters, ['format' => 'xlsx'])) }}" class="glass-secondary px-5 py-2 text-sm font-semibold">Export Excel</a>
                    <a href="{{ route('admin.operations.export', array_merge($filters, ['format' => 'pdf'])) }}" class="glass-secondary px-5 py-2 text-sm font-semibold">Export PDF</a>
                @endcan
            </div>
        </div>

        <x-table :headers="['Scheduled At', 'Patient', 'Doctor', 'Requested By', 'Room', 'Status', 'Staff', 'Duration', 'Actions']">
            @forelse ($operations as $operation)
                <tr class="text-sm text-slate-200/90">
                    <td class="px-4 py-3">{{ $operation->scheduled_at->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3">{{ $operation->patient?->name }}</td>
                    <td class="px-4 py-3">{{ $operation->doctor?->name }}</td>
                    <td class="px-4 py-3 text-xs text-slate-400/80">{{ $operation->requestedDoctor?->name ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $operation->room?->name }}</td>
                    <td class="px-4 py-3 capitalize">
                        <form action="{{ route('operations.status', $operation) }}" method="POST" class="inline-flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            @php
                                $rowStatusOptions = collect($statuses)->mapWithKeys(function ($status) {
                                    return [$status->value => \Illuminate\Support\Str::headline($status->value)];
                                })->toArray();
                                $isStatusLocked = $operation->status === App\Enums\OperationStatus::PendingApproval && $operation->requestedDoctor;
                            @endphp
                            <x-ui.select model="status" :options="$rowStatusOptions" :selected="$operation->status->value" :auto-submit="true" :disabled="$isStatusLocked" class="min-w-[160px]" />
                        </form>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-2">
                            @forelse ($operation->staffMembers as $staff)
                                @php($pivotStatus = $staff->pivot->status)
                                <span class="rounded-full px-3 py-1 text-xs {{ $pivotStatus === App\Enums\OperationStaffStatus::Approved->value ? 'glass-chip' : 'bg-amber-500/20 text-amber-200 border border-amber-300/30' }}">
                                    {{ $staff->user?->name }}
                                </span>
                            @empty
                                <span class="text-xs text-slate-400">—</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-4 py-3">{{ $operation->estimated_duration_minutes }} mins</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            @if ($operation->status === App\Enums\OperationStatus::PendingApproval && $operation->requestedDoctor)
                                <form action="{{ route('admin.operations.approve-request', $operation) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="glass-primary px-3 py-1 text-xs">Approve</button>
                                </form>
                                <form action="{{ route('admin.operations.reject-request', $operation) }}" method="POST" class="inline" onsubmit="return confirm('Reject this doctor request?');">
                                    @csrf
                                    <button type="submit" class="rounded-full border border-red-400/70 bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-200 hover:bg-red-500/20">Reject</button>
                                </form>
                            @endif
                            @can('access-admin')
                                <a href="{{ route('admin.operations.edit', $operation) }}" class="glass-secondary px-3 py-1 text-xs font-semibold">Edit</a>
                                <form action="{{ route('admin.operations.destroy', $operation) }}" method="POST" onsubmit="return confirm('Delete this operation?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-full border border-red-400/70 bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-200">Delete</button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-4 text-center text-sm text-slate-400">No operations found.</td>
                </tr>
            @endforelse
            <tr>
                <td colspan="7" class="px-4 py-4">
                    {{ $operations->links() }}
                </td>
            </tr>
        </x-table>
    </div>
@endsection
