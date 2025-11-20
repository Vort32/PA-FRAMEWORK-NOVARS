<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use App\Models\OperationRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'gender',
        'date_of_birth',
        'address',
        'medical_record_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'date_of_birth' => 'date',
        ];
    }

    public function patientProfile(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    public function doctorProfile(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function staffProfile(): HasOne
    {
        return $this->hasOne(Staff::class);
    }

    public function patientOperations(): HasMany
    {
        return $this->hasMany(Operation::class, 'patient_id');
    }

    public function operationRequests(): HasMany
    {
        return $this->hasMany(OperationRequest::class, 'patient_id');
    }

    public function doctorOperations(): HasMany
    {
        return $this->hasMany(Operation::class, 'doctor_id');
    }

    public function operationReports(): HasMany
    {
        return $this->hasMany(OperationReport::class, 'doctor_id');
    }

    public function isRole(UserRole $role): bool
    {
        return $this->role === $role;
    }
}
