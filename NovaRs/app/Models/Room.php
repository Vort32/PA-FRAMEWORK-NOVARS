<?php

namespace App\Models;

use App\Enums\RoomStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'status',
        'capacity',
        'notes',
    ];

    protected $casts = [
        'status' => RoomStatus::class,
    ];

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }

    public function equipments(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class, 'room_equipments')->withPivot('quantity')->withTimestamps();
    }
}
