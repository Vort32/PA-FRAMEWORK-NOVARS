<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a202c; }
        h1 { color: #2B6CB0; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #cbd5f5; padding: 8px; text-align: left; }
        th { background-color: #2B6CB0; color: #fff; font-size: 11px; text-transform: uppercase; }
        .meta { font-size: 11px; color: #4a5568; margin-top: 4px; }
    </style>
</head>
<body>
    <h1>Operation Report</h1>
    <p class="meta">Generated at: {{ now()->format('d M Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th>Scheduled At</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Room</th>
                <th>Status</th>
                <th>Disease</th>
                <th>Duration</th>
                <th>Outcome</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($operations as $operation)
                <tr>
                    <td>{{ optional($operation->scheduled_at)->format('d M Y H:i') }}</td>
                    <td>{{ $operation->patient?->name }}</td>
                    <td>{{ $operation->doctor?->name }}</td>
                    <td>{{ $operation->room?->name }}</td>
                    <td>{{ ucfirst($operation->status->value ?? $operation->status) }}</td>
                    <td>{{ $operation->disease?->name }}</td>
                    <td>{{ $operation->estimated_duration_minutes }} mins</td>
                    <td>{{ ucfirst($operation->report?->status_outcome->value ?? $operation->report?->status_outcome) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No operations found for the selected filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
