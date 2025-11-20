<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Operation;
use App\Models\OperationReport;
use App\Models\User;

class OperationReportPolicy
{
    public function create(User $user, Operation $operation): bool
    {
        return $user->isRole(UserRole::Doctor) && $operation->doctor_id === $user->id;
    }

    public function update(User $user, OperationReport $report): bool
    {
        return $user->isRole(UserRole::Doctor) && $report->doctor_id === $user->id;
    }
}
