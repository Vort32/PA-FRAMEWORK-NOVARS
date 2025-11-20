@extends('layouts.patient')

@php $title = 'Request Surgery'; @endphp

@section('content')
    <x-card title="Operation Request">
        @php
            $diseaseOptions = ['' => 'Not sure / other'] + $diseases->pluck('name', 'id')->toArray();
            $doctorOptions = ['' => 'Select doctor'] + $doctors->pluck('name', 'id')->toArray();
        @endphp

        <form method="POST" action="{{ route('patient.operation-requests.store', [], false) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <x-ui.select label="Preferred Doctor" model="doctor_id" :options="$doctorOptions" :selected="old('doctor_id')" />
                @error('doctor_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-ui.select label="Suspected Disease" model="disease_id" :options="$diseaseOptions" :selected="old('disease_id')" placeholder="Not sure / other" />
                @error('disease_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Describe your symptoms</label>
                <textarea name="symptoms_description" rows="5" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30" placeholder="Explain what you are experiencing and why you believe surgery is needed...">{{ old('symptoms_description') }}</textarea>
                @error('symptoms_description')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Preferred date (optional)</label>
                <input type="date" name="preferred_date" value="{{ old('preferred_date') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
                @error('preferred_date')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Referral Letter (optional)</label>
                <input type="file" name="referral_letter" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
                <p class="mt-1 text-xs text-gray-500">Attach supporting document for the selected doctor (PDF, JPG, PNG · max 5MB).</p>
                @error('referral_letter')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('patient.dashboard', [], false) }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600">Cancel</a>
                <button type="submit" class="rounded-lg bg-[#2B6CB0] px-4 py-2 text-sm font-semibold text-white hover:bg-[#1E4E82]">Submit Request</button>
            </div>
        </form>
    </x-card>
@endsection
