<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-sm font-medium text-gray-700">Name</label>
        <input type="text" name="name" value="{{ old('name', $equipment->name ?? '') }}" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>
    <div>
        <label class="text-sm font-medium text-gray-700">Category</label>
        <input type="text" name="category" value="{{ old('category', $equipment->category ?? '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>
    <div>
        <label class="text-sm font-medium text-gray-700">Serial Number</label>
        <input type="text" name="serial_number" value="{{ old('serial_number', $equipment->serial_number ?? '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>
    <div>
        <label class="text-sm font-medium text-gray-700">Quantity Available</label>
        <input type="number" min="0" name="quantity_available" value="{{ old('quantity_available', $equipment->quantity_available ?? 0) }}" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">
    </div>
    <div class="md:col-span-2">
        <label class="text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" rows="3" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-[#2B6CB0] focus:outline-none focus:ring-2 focus:ring-[#2B6CB0]/30">{{ old('description', $equipment->description ?? '') }}</textarea>
    </div>
</div>
