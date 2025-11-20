@php $isStaff = auth()->check() && auth()->user()->isRole(\App\Enums\UserRole::Staff); @endphp

@extends($isStaff ? 'layouts.staff' : 'layouts.admin')

@php $title = 'Operation Request Details'; @endphp

@section('content')
    <div class="grid gap-8 lg:grid-cols-3">
        <x-card title="Request Information" class="lg:col-span-2">
            <dl class="grid gap-4 text-sm text-slate-200/90">
                <div class="flex justify-between">
                    <dt class="font-medium text-slate-400/80">Submitted</dt>
                    <dd>{{ $request->created_at->format('d M Y H:i') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="font-medium text-slate-400/80">Patient</dt>
                    <dd>{{ $request->patient?->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="font-medium text-slate-400/80">Assigned Doctor</dt>
                    <dd>{{ $request->doctor?->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="font-medium text-slate-400/80">Preferred Date</dt>
                    <dd>{{ $request->preferred_date?->format('d M Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-slate-400/80">Suspected Disease</dt>
                    <dd class="mt-1">{{ $request->disease?->name ?? 'Not specified' }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-slate-400/80">Symptoms</dt>
                    <dd class="mt-1 whitespace-pre-line text-slate-200/80">{{ $request->symptoms_description ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="font-medium text-slate-400/80">Status</dt>
                    <dd class="capitalize">
                        <span class="glass-badge text-xs">{{ ucfirst($request->status->value) }}</span>
                    </dd>
                </div>
                @if ($request->referral_letter_path)
                    <div>
                        <dt class="font-medium text-slate-400/80">Referral Letter</dt>
                        <dd class="mt-1">
                            <a href="{{ route('operation-requests.referral-letter', [$request], false) }}" target="_blank" class="glass-secondary inline-flex items-center gap-2 px-4 py-1.5 text-xs font-semibold">
                                <i data-lucide="paperclip" class="h-3.5 w-3.5"></i>
                                {{ $request->referral_letter_original_name ?? 'View Letter' }}
                            </a>
                        </dd>
                    </div>
                @endif
                @if ($request->admin_notes)
                    <div>
                        <dt class="font-medium text-slate-400/80">Admin Notes</dt>
                        <dd class="mt-1 text-slate-200/80">{{ $request->admin_notes }}</dd>
                    </div>
                @endif
                @if ($request->doctor_notes)
                    <div>
                        <dt class="font-medium text-slate-400/80">Doctor Notes</dt>
                        <dd class="mt-1 text-slate-200/80">{{ $request->doctor_notes }}</dd>
                    </div>
                @endif
                @if ($request->operation)
                    <div>
                        <dt class="font-medium text-slate-400/80">Scheduled Operation</dt>
                        <dd class="mt-1 text-slate-200/80">
                            {{ $request->operation->scheduled_at->format('d M Y H:i') }} — Room {{ $request->operation->room?->name }}
                        </dd>
                    </div>
                @endif
            </dl>
        </x-card>

        <div class="flex flex-col gap-6">
            @if ($request->status === \App\Enums\OperationRequestStatus::Approved && !$request->operation)
                <x-card title="Approve Request">
                    <form method="POST" action="{{ route('admin.operation-requests.approve', [$request], false) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-400/80">Scheduled Date & Time</label>
                            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required class="mt-2 w-full rounded-2xl border border-white/15 bg-white/5 px-3 py-2 text-slate-100 focus:border-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                            @error('scheduled_at')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            @php
                                $roomOptions = collect($rooms)->mapWithKeys(function ($room) {
                                    $statusValue = $room['status'] instanceof \BackedEnum ? $room['status']->value : $room['status'];

                                    return [$room['id'] => $room['name'].' ('.\Illuminate\Support\Str::headline($statusValue).')'];
                                });

                                $roomOptions = ['' => 'Select room'] + $roomOptions->toArray();
                            @endphp
                            <x-ui.select label="Assign Room" model="room_id" :options="$roomOptions" :selected="old('room_id')" class="mt-2" />
                            @error('room_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            @php
                                $diseaseOptions = ['' => 'Keep patient selection'] + $diseases->pluck('name', 'id')->toArray();
                            @endphp
                            <x-ui.select label="Confirm Diagnosis (optional)" model="disease_id" :options="$diseaseOptions" :selected="old('disease_id', $request->disease_id)" class="mt-2" />
                            @error('disease_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-400/80">Estimated Duration (minutes)</label>
                                <input type="number" name="estimated_duration_minutes" min="15" step="15" value="{{ old('estimated_duration_minutes', 60) }}" class="mt-2 w-full rounded-2xl border border-white/15 bg-white/5 px-3 py-2 text-slate-100 focus:border-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                                @error('estimated_duration_minutes')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-400/80">Admin Notes</label>
                                <input type="text" name="admin_notes" value="{{ old('admin_notes') }}" class="mt-2 w-full rounded-2xl border border-white/15 bg-white/5 px-3 py-2 text-slate-100 focus:border-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">
                                @error('admin_notes')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-400/80">Additional Notes for Operation</label>
                            <textarea name="notes" rows="3" class="mt-2 w-full rounded-2xl border border-white/15 bg-white/5 px-3 py-2 text-slate-100 focus:border-white/40 focus:outline-none focus:ring-2 focus:ring-white/20">{{ old('notes', $request->symptoms_description) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="glass-primary w-full px-4 py-2 text-sm">Approve & Schedule</button>
                    </form>
                </x-card>

                <x-card title="Reject Request">
                    <form method="POST" action="{{ route('admin.operation-requests.reject', [$request], false) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-400/80">Reason</label>
                            <textarea name="admin_notes" rows="3" required class="mt-2 w-full rounded-2xl border border-red-300/40 bg-red-500/5 px-3 py-2 text-slate-100 focus:border-red-300/60 focus:outline-none focus:ring-2 focus:ring-red-400/30">{{ old('admin_notes') }}</textarea>
                            @error('admin_notes')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full rounded-full border border-red-400/60 bg-red-500/10 px-4 py-2 text-sm font-semibold text-red-200 hover:bg-red-500/20">Reject Request</button>
                    </form>
                </x-card>
            @elseif ($request->status === \App\Enums\OperationRequestStatus::Pending)
                <x-card title="Awaiting Doctor Review">
                    <p class="text-sm text-slate-300/80">This request is waiting for the assigned doctor to review and approve the referral.</p>
                </x-card>
            @else
                <x-card title="Request Processed">
                    <p class="text-sm text-slate-300/80">This request has been {{ $request->status->value }}.</p>
                    <a href="{{ route('admin.operation-requests.index', [], false) }}" class="mt-4 inline-flex items-center justify-center glass-secondary px-4 py-2 text-sm font-medium hover:shadow-lg hover:shadow-slate-900/30">Back to list</a>
                </x-card>
            @endif
        </div>
    </div>
@endsection
