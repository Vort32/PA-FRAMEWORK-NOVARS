<?php

namespace App\Models;

use App\Enums\OperationOutcomeStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'operation_id',
        'doctor_id',
        'status_outcome',
        'complications',
        'procedure_details',
        'duration_minutes',
    ];

    protected $casts = [
        'status_outcome' => OperationOutcomeStatus::class,
    ];

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
