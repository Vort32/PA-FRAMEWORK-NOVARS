<?php

namespace App\Enums;

enum OperationOutcomeStatus: string
{
    case Success = 'success';
    case Complication = 'complication';
    case Failure = 'failure';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
