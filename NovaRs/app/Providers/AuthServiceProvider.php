<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\Operation;
use App\Models\OperationReport;
use App\Policies\OperationReportPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        OperationReport::class => OperationReportPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('access-admin', fn ($user) => $user->isRole(UserRole::Admin));
        Gate::define('access-doctor', fn ($user) => $user->isRole(UserRole::Doctor));
        Gate::define('access-staff', fn ($user) => $user->isRole(UserRole::Staff));
        Gate::define('access-patient', fn ($user) => $user->isRole(UserRole::Patient));

        Gate::define('submit-operation-report', function ($user, Operation $operation) {
            return $user->isRole(UserRole::Doctor) && $operation->doctor_id === $user->id;
        });

        Gate::define('manage-operations', function ($user) {
            return in_array($user->role, [UserRole::Admin, UserRole::Staff], true);
        });
    }
}
