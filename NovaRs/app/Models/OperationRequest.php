<?php

namespace App\Models;

use App\Enums\OperationRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'disease_id',
        'symptoms_description',
        'referral_letter_path',
        'referral_letter_original_name',
        'preferred_date',
        'status',
        'admin_notes',
        'doctor_notes',
        'operation_id',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'status' => OperationRequestStatus::class,
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class);
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }
}
