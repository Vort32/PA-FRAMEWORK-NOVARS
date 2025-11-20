@php($user = $patient->user ?? null)

<div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="text-sm font-medium text-gray-700">Full Name</label>
        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>

    <div>
        <label class="text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>

    <div>
        <label class="text-sm font-medium text-gray-700">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>

    <div>
        @php
            $genderOptions = ['' => 'Select gender', 'male' => 'Male', 'female' => 'Female'];
        @endphp
        <x-ui.select label="Gender" model="gender" :options="$genderOptions" :selected="old('gender', $user->gender ?? '')" />
    </div>

    <div>
        <label class="text-sm font-medium text-gray-700">Date of Birth</label>
        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($user->date_of_birth ?? null)->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>

    <div>
        <label class="text-sm font-medium text-gray-700">Password</label>
        <input type="password" name="password" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30" placeholder="Leave blank to keep current">
    </div>

    <div class="md:col-span-2">
        <label class="text-sm font-medium text-gray-700">Address</label>
        <textarea name="address" rows="2" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">{{ old('address', $user->address ?? '') }}</textarea>
    </div>

    <div>
        <label class="text-sm font-medium text-gray-700">Emergency Contact Name</label>
        <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $patient->emergency_contact_name ?? '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>

    <div>
        <label class="text-sm font-medium text-gray-700">Emergency Contact Phone</label>
        <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone ?? '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>

    <div>
        <label class="text-sm font-medium text-gray-700">Blood Type</label>
        <input type="text" name="blood_type" value="{{ old('blood_type', $patient->blood_type ?? '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>

    <div>
        <label class="text-sm font-medium text-gray-700">Allergies</label>
        <textarea name="allergies" rows="2" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">{{ old('allergies', $patient->allergies ?? '') }}</textarea>
    </div>

    <div class="md:col-span-2">
        <label class="text-sm font-medium text-gray-700">Medical History</label>
        <textarea name="medical_history" rows="3" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">{{ old('medical_history', $patient->medical_history ?? '') }}</textarea>
    </div>
</div>
