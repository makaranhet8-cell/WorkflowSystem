<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // កែសម្រួលត្រង់នេះ (ប្រើ User មិនមែន AuthUser)
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image'
        // 'role', // លុបចេញ ប្រសិនបើអ្នកប្រើ Spatie Roles table
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * ជំនួសឱ្យការឆែក Column 'role' យើងប្រើ Function របស់ Spatie វិញ
     */

    public function isApproverOrDepartmentAdmin(): bool
    {
        return $this->hasAnyRole(['admin', 'approver', 'team_leader', 'ceo', 'hr_manager', 'cfo', 'department_admin']);
    }
    public function isDepartmentAdmin(): bool
    {
        return $this->hasRole('department_admin');
    }
    public function isApprover(): bool
    {
        return $this->hasRole('approver');
    }

    public function isAdmin(): bool
    {
        // ប្រើតែ 'admin' ឱ្យស្របតាម Seeder របស់អ្នក
        return $this->hasRole('admin');
    }

    public function canAccessApproverDashboard(): bool
    {
        // រួមបញ្ចូល Role ទាំងអស់ដែលមានសិទ្ធិអនុម័ត
        return $this->hasAnyRole(['admin', 'approver', 'team_leader', 'ceo', 'hr_manager', 'cfo']);
    }

    // --- Relationships ---

    public function leaveRequests(): HasMany
    {
        // វានឹងឈប់បង្ហាញ Error 'Undefined method hasMany'
        return $this->hasMany(LeaveRequest::class);
    }

    public function missionRequests(): HasMany
    {
        return $this->hasMany(MissionRequest::class);
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_user', 'user_id', 'department_id');
    }

    // --- Helpers សម្រាប់ Check Role នីមួយៗ (Optional) ---

    public function isTeamLeader(): bool { return $this->hasRole('team_leader'); }
    public function isHR(): bool { return $this->hasRole('hr_manager'); }
    public function isCEO(): bool { return $this->hasRole('ceo'); }
    public function isCFO(): bool { return $this->hasRole('cfo'); }
}
