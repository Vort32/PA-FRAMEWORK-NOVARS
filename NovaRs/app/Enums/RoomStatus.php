<?php

namespace App\Enums;

enum RoomStatus: string
{
    case Available = 'available';
    case InUse = 'in_use';
    case Cleaning = 'cleaning';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
