<?php

namespace App\Models;

use App\Enums\OperationStatus;
use App\Models\OperationRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Staff;
use App\Models\User;

/** @property int|null $requested_doctor_id */

class Operation extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'requested_doctor_id',
        'room_id',
        'disease_id',
        'scheduled_at',
        'status',
        'estimated_duration_minutes',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'status' => OperationStatus::class,
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function requestedDoctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_doctor_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class);
    }

    public function equipments(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class, 'operation_equipments')
            ->using(OperationEquipment::class)
            ->withPivot(['quantity', 'notes'])
            ->withTimestamps();
    }

    public function staffMembers(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'operation_staff')
            ->withPivot(['status'])
            ->withTimestamps();
    }

    public function report(): HasOne
    {
        return $this->hasOne(OperationReport::class);
    }

    public function request(): HasOne
    {
        return $this->hasOne(OperationRequest::class);
    }
}
