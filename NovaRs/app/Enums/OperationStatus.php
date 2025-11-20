<?php

namespace App\Enums;

enum OperationStatus: string
{
    case Scheduled = 'scheduled';
    case Ongoing = 'ongoing';
    case Completed = 'completed';
    case Postponed = 'postponed';
    case Cancelled = 'cancelled';
    case PendingAssignment = 'pending_assignment';
    case PendingApproval = 'pending_approval';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
