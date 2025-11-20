<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Disease extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icd_code',
        'description',
    ];

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }
}
