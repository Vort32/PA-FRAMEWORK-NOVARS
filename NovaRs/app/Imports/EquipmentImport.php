<?php

namespace App\Imports;

use App\Models\Equipment;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EquipmentImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row): Equipment
    {
        $normalized = $this->normalizeRow($row);

        $identifier = $normalized['serial_number'] ?? null;

        $attributes = [
            'name' => $normalized['name'],
            'category' => $normalized['category'] ?? null,
            'serial_number' => $identifier,
            'quantity_available' => (int) ($normalized['quantity_available'] ?? 0),
            'description' => $normalized['description'] ?? null,
        ];

        if ($identifier) {
            return Equipment::updateOrCreate(['serial_number' => $identifier], $attributes);
        }

        return Equipment::updateOrCreate(
            ['name' => $normalized['name']],
            $attributes
        );
    }

    public function rules(): array
    {
        return [
            '*.name' => ['required', 'string', 'max:255'],
            '*.category' => ['nullable', 'string', 'max:255'],
            '*.serial_number' => ['nullable', 'string', 'max:255'],
            '*.quantity_available' => ['required', 'integer', 'min:0'],
            '*.description' => ['nullable', 'string'],
        ];
    }

    protected function normalizeRow(array $row): array
    {
        $row = array_change_key_case($row, CASE_LOWER);

        return Arr::only(array_merge([
            'name' => null,
            'category' => null,
            'serial_number' => null,
            'quantity_available' => 0,
            'description' => null,
        ], $row), ['name', 'category', 'serial_number', 'quantity_available', 'description']);
    }
}
