<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Doctor = 'doctor';
    case Staff = 'staff';
    case Patient = 'patient';

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Admin => 'admin.dashboard',
            self::Doctor => 'doctor.dashboard',
            self::Staff => 'staff.dashboard',
            self::Patient => 'patient.dashboard',
        };
    }
}
