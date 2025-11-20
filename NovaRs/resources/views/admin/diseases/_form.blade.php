<div class="space-y-4">
    <div>
        <label class="text-sm font-medium text-gray-700">Name</label>
        <input type="text" name="name" value="{{ old('name', $disease->name ?? '') }}" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>
    <div>
        <label class="text-sm font-medium text-gray-700">ICD Code</label>
        <input type="text" name="icd_code" value="{{ old('icd_code', $disease->icd_code ?? '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>
    <div>
        <label class="text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" rows="3" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">{{ old('description', $disease->description ?? '') }}</textarea>
    </div>
</div>
