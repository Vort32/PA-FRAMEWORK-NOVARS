<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OperationEquipment extends Pivot
{
    protected $table = 'operation_equipments';

    protected $fillable = [
        'operation_id',
        'equipment_id',
        'quantity',
        'notes',
    ];
}
