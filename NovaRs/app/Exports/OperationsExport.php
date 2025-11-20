<?php

namespace App\Exports;

use App\Enums\OperationStatus;
use App\Models\Operation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OperationsExport implements FromCollection, WithMapping, WithHeadings
{
    protected ?Collection $cache = null;

    public function __construct(protected array $filters = [])
    {
    }

    public function collection(): Collection
    {
        if ($this->cache) {
            return $this->cache;
        }

        $query = Operation::with(['patient', 'doctor', 'room', 'disease', 'report'])
            ->orderBy('scheduled_at');

        $this->applyFilters($query);

        return $this->cache = $query->get();
    }

    public function headings(): array
    {
        return [
            'Scheduled At',
            'Status',
            'Patient',
            'Doctor',
            'Room',
            'Disease',
            'Estimated Duration (minutes)',
            'Notes',
            'Outcome',
            'Report Duration (minutes)',
        ];
    }

    public function map($operation): array
    {
        return [
            optional($operation->scheduled_at)->format('d M Y H:i'),
            $operation->status?->value ?? $operation->status,
            $operation->patient?->name,
            $operation->doctor?->name,
            $operation->room?->name,
            $operation->disease?->name,
            $operation->estimated_duration_minutes,
            $operation->notes,
            optional($operation->report?->status_outcome)?->value,
            $operation->report?->duration_minutes,
        ];
    }

    public function downloadPdf(string $fileName)
    {
        $operations = $this->collection();

        return Pdf::loadView('admin.operations.export-pdf', [
            'operations' => $operations,
            'filters' => $this->filters,
        ])->setPaper('a4', 'landscape')->download($fileName);
    }

    protected function applyFilters(Builder $query): void
    {
        $filters = $this->filters;

        $query->when(! empty($filters['from']), fn (Builder $q) => $q->whereDate('scheduled_at', '>=', $filters['from']))
            ->when(! empty($filters['to']), fn (Builder $q) => $q->whereDate('scheduled_at', '<=', $filters['to']))
            ->when(! empty($filters['doctor_id']), fn (Builder $q) => $q->where('doctor_id', $filters['doctor_id']))
            ->when(! empty($filters['room_id']), fn (Builder $q) => $q->where('room_id', $filters['room_id']))
            ->when(! empty($filters['status']), function (Builder $q) use ($filters) {
                if (in_array($filters['status'], OperationStatus::values(), true)) {
                    $q->where('status', $filters['status']);
                }
            });
    }
}
