@php($user = $staff->user ?? null)

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
        <label class="text-sm font-medium text-gray-700">Position</label>
        <input type="text" name="position" value="{{ old('position', $staff->position ?? '') }}" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>
    <div>
        <x-ui.select label="Shift Type" model="shift_type" :options="['day' => 'Day', 'night' => 'Night']" :selected="old('shift_type', $staff->shift_type ?? '')" />
    </div>
</div>
