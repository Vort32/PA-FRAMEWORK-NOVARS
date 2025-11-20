@extends('layouts.doctor')

@php $title = 'Referral Detail'; @endphp

@section('content')
    <div class="grid gap-8 lg:grid-cols-3">
        <x-card title="Patient Information" class="lg:col-span-2">
            <dl class="grid gap-4 text-sm text-slate-700">
                <div class="flex justify-between">
                    <dt class="font-medium text-slate-500">Submitted</dt>
                    <dd>{{ $request->created_at->format('d M Y H:i') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="font-medium text-slate-500">Patient</dt>
                    <dd>{{ $request->patient?->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="font-medium text-slate-500">Preferred Date</dt>
                    <dd>{{ $request->preferred_date?->format('d M Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-slate-500">Suspected Disease</dt>
                    <dd class="mt-1">{{ $request->disease?->name ?? 'Not specified' }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-slate-500">Symptoms Description</dt>
                    <dd class="mt-1 whitespace-pre-line text-slate-600">{{ $request->symptoms_description ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="font-medium text-slate-500">Status</dt>
                    <dd>
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-600">
                            {{ \Illuminate\Support\Str::headline($request->status->value) }}
                        </span>
                    </dd>
                </div>
                @if ($request->referral_letter_path)
                    <div>
                        <dt class="font-medium text-slate-500">Referral Letter</dt>
                        <dd class="mt-1">
                            <a href="{{ route('operation-requests.referral-letter', [$request], false) }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl border border-[#2B6CB0] px-4 py-1.5 text-xs font-semibold text-[#2B6CB0] transition hover:bg-[#2B6CB0] hover:text-white">
                                <i data-lucide="paperclip" class="h-3.5 w-3.5"></i>
                                {{ $request->referral_letter_original_name ?? 'View Letter' }}
                            </a>
                        </dd>
                    </div>
                @endif
                @if ($request->doctor_notes && $request->status !== \App\Enums\OperationRequestStatus::Pending)
                    <div>
                        <dt class="font-medium text-slate-500">Your Notes</dt>
                        <dd class="mt-1 text-slate-600">{{ $request->doctor_notes }}</dd>
                    </div>
                @endif
                @if ($request->admin_notes)
                    <div>
                        <dt class="font-medium text-slate-500">Admin Notes</dt>
                        <dd class="mt-1 text-slate-600">{{ $request->admin_notes }}</dd>
                    </div>
                @endif
            </dl>
        </x-card>

        <div class="flex flex-col gap-6">
            @if ($request->status === \App\Enums\OperationRequestStatus::Pending)
                <x-card title="Approve Referral">
                    <form method="POST" action="{{ route('doctor.operation-requests.approve', $request, false) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="text-sm font-medium text-slate-600">Notes for Admin (optional)</label>
                            <textarea name="doctor_notes" rows="3" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">{{ old('doctor_notes') }}</textarea>
                            @error('doctor_notes')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full rounded-xl bg-[#2B6CB0] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#1E4E82]">Approve &amp; Forward</button>
                    </form>
                </x-card>

                <x-card title="Reject Referral">
                    <form method="POST" action="{{ route('doctor.operation-requests.reject', $request, false) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="text-sm font-medium text-slate-600">Reason</label>
                            <textarea name="doctor_notes" rows="3" required class="mt-1 w-full rounded-xl border border-red-200 px-3 py-2 text-sm text-slate-700 focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-300/40">{{ old('doctor_notes') }}</textarea>
                            @error('doctor_notes')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full rounded-xl border border-red-300 bg-red-500/10 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-500/20">Reject Request</button>
                    </form>
                </x-card>
            @else
                <x-card title="Referral Status">
                    <p class="text-sm text-slate-500">This referral has been {{ $request->status->value }}.</p>
                    <a href="{{ route('doctor.operation-requests.index', [], false) }}" class="mt-4 inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100">
                        Back to list
                        <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    </a>
                </x-card>
            @endif
        </div>
    </div>
@endsection
