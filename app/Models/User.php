<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\LeaveRequest;
use App\Models\MissionRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isApprover(): bool
    {
        return $this->role === 'approver';
    }

    public function isDepartmentAdmin(): bool
    {
        return in_array($this->role, ['admin', 'system_admin']);
        return $this->role === 'admin';

    }
    public function isApproverOrDepartmentAdmin()
{

    return in_array($this->role, ['admin', 'system_admin','approver', 'team_leader', 'ceo', 'hr_manager','cfo']);
}

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function missionRequests(): HasMany
    {
        return $this->hasMany(MissionRequest::class);
    }

    // App\Models\User.php
public function departments()
{
    return $this->belongsToMany(Department::class, 'department_user', 'user_id', 'department_id');
}
    public function hasRole($role): bool
    {
        return $this->role === $role;
    }

    // ឆែកថាជា Team Leader ឬអត់
    public function isTeamLeader(): bool
    {
        return $this->role === 'team_leader';
    }

    // ឆែកថាជា HR ឬអត់
    public function isHR(): bool
    {
        return $this->role === 'hr_manager';
    }

    // ឆែកថាជា CEO ឬអត់
    public function isCEO(): bool
    {
        return $this->role === 'ceo';
    }
    public function isCFO(): bool
    {
        return $this->role === 'cfo';
    }
}
