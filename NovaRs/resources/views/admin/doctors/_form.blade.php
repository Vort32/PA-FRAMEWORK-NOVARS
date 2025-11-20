@php($user = $doctor?->user)

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-sm font-medium text-gray-700">Full Name</label>
        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>
    <div>
        <label class="text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>
    <div>
        <label class="text-sm font-medium text-gray-700">Password</label>
        <input type="password" name="password" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30" placeholder="Leave blank to keep current">
    </div>
    <div>
        <label class="text-sm font-medium text-gray-700">Specialization</label>
        <input type="text" name="specialization" value="{{ old('specialization', $doctor?->specialization ?? '') }}" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>
    <div>
        <label class="text-sm font-medium text-gray-700">License Number</label>
        <input type="text" name="license_number" value="{{ old('license_number', $doctor?->license_number ?? '') }}" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>
    <div>
        <label class="text-sm font-medium text-gray-700">Years of Experience</label>
        <input type="number" min="0" name="years_of_experience" value="{{ old('years_of_experience', $doctor?->years_of_experience ?? '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>
    <div class="md:col-span-2">
        <label class="text-sm font-medium text-gray-700">Biography</label>
        <textarea name="bio" rows="3" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">{{ old('bio', $doctor?->bio ?? '') }}</textarea>
    </div>
</div>
