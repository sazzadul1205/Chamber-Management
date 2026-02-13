<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    // =======================
    // Mass Assignable Fields
    // =======================
    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'password',
        'role_id',
        'status',
        'last_login_at',
        'last_login_device_id',
        'current_session_id',
        'blood_group',
    ];

    // =======================
    // Hidden Fields
    // =======================
    protected $hidden = [
        'password',
        'remember_token',
        'current_session_id', // Hide session ID for security
    ];

    // =======================
    // Attribute Casting
    // =======================
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'deleted_at' => 'datetime',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // =======================
    // Default Attributes
    // =======================
    protected $attributes = [
        'status' => 'active',
        'role_id' => 1, // Default to lowest role if not specified
    ];

    // =======================
    // Relationships
    // =======================

    /**
     * Role relationship
     */
    public function role()
    {
        return $this->belongsTo(Role::class)->withDefault([
            'name' => 'Unknown',
            'permissions' => [],
        ]);
    }

    /**
     * Doctor profile relationship
     */
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    /**
     * total count of users (for dashboard stats)
     */
    public static function totalCount(): int
    {
        return static::count();
    }

    /**
     * Appointments created by this user
     */
    public function createdAppointments()
    {
        return $this->hasMany(Appointment::class, 'created_by');
    }

    /**
     * Appointments updated by this user
     */
    public function updatedAppointments()
    {
        return $this->hasMany(Appointment::class, 'updated_by');
    }

    /**
     * Treatments created by this user
     */
    public function createdTreatments()
    {
        return $this->hasMany(Treatment::class, 'created_by');
    }

    /**
     * Audit logs for this user
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // =======================
    // Query Scopes
    // =======================

    /**
     * Only active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Only inactive users
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Only suspended users
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    /**
     * Filter by role
     */
    public function scopeByRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    /**
     * Filter by role name
     */
    public function scopeByRoleName($query, $roleName)
    {
        return $query->whereHas('role', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    /**
     * Users who have logged in recently
     */
    public function scopeRecentlyActive($query, $hours = 24)
    {
        return $query->whereNotNull('last_login_at')
            ->where('last_login_at', '>=', now()->subHours($hours));
    }

    /**
     * Users with specific blood group
     */
    public function scopeBloodGroup($query, $bloodGroup)
    {
        return $query->where('blood_group', $bloodGroup);
    }

    // =======================
    // Role Helper Methods
    // =======================

    public function isSuperAdmin()
    {
        return $this->role_id === 1;
    }

    public function isAdmin()
    {
        return in_array($this->role_id, [1, 2]);
    }

    public function isDoctor()
    {
        return $this->role_id === 3;
    }

    public function isStaff()
    {
        return $this->role_id === 4;
    }

    public function isAccountant()
    {
        return $this->role_id === 5;
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($roleName)
    {
        if (is_array($roleName)) {
            return in_array($this->role->name, $roleName);
        }
        return $this->role->name === $roleName;
    }

    /**
     * Check if user has permission (through role)
     */
    public function hasPermission($permission)
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role.permissions');
        }

        return $this->role->permissions->contains('name', $permission);
    }

    /**
     * Get role name
     */
    public function getRoleNameAttribute()
    {
        return $this->role->name ?? 'Unknown';
    }

    /**
     * Get display name (first name or full name)
     */
    public function getDisplayNameAttribute()
    {
        $names = explode(' ', $this->full_name);
        return $names[0] ?? $this->full_name;
    }

    /**
     * Get last login time in human readable format
     */
    public function getLastLoginHumanAttribute()
    {
        return $this->last_login_at ? $this->last_login_at->diffForHumans() : 'Never';
    }

    /**
     * Check if user is currently online (active within last 5 minutes)
     */
    public function getIsOnlineAttribute()
    {
        if (!$this->last_login_at) {
            return false;
        }

        return $this->last_login_at->diffInMinutes(now()) < 5;
    }

    /**
     * Get Tailwind-friendly status badge
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => [
                'color' => 'green',
                'icon' => '✓',
            ],
            'inactive' => [
                'color' => 'gray',
                'icon' => '⏸️',
            ],
            'suspended' => [
                'color' => 'red',
                'icon' => '⛔',
            ],
        ];

        $config = $badges[$this->status] ?? $badges['inactive'];

        return '<span class="bg-' . $config['color'] . '-100 text-' . $config['color'] . '-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded inline-flex items-center">'
            . '<span class="mr-1">' . $config['icon'] . '</span>'
            . ucfirst($this->status)
            . '</span>';
    }

    /**
     * Get blood group with icon/emoji
     */
    public function getBloodGroupDisplayAttribute()
    {
        if (!$this->blood_group) {
            return 'Not set';
        }

        $icons = [
            'A+' => '🅰️➕',
            'A-' => '🅰️➖',
            'B+' => '🅱️➕',
            'B-' => '🅱️➖',
            'AB+' => '🆎➕',
            'AB-' => '🆎➖',
            'O+' => '🅾️➕',
            'O-' => '🅾️➖',
        ];

        $icon = $icons[$this->blood_group] ?? '🩸';
        return $icon . ' ' . $this->blood_group;
    }

    /**
     * Check if user can be deactivated (prevent self-deactivation)
     */
    public function canBeDeactivated()
    {
        // Super admin cannot be deactivated by anyone
        if ($this->isSuperAdmin()) {
            return false;
        }

        // Users cannot deactivate themselves
        return auth()->id() !== $this->id;
    }

    /**
     * Update last login information
     */
    public function updateLastLogin($deviceId = null, $sessionId = null)
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_device_id' => $deviceId ?? request()->header('User-Agent'),
            'current_session_id' => $sessionId ?? session()->getId(),
        ]);
    }

    /**
     * Clear current session (for logout)
     */
    public function clearSession()
    {
        $this->update(['current_session_id' => null]);
    }

    /**
     * Check if this session is the current active session
     */
    public function isCurrentSession($sessionId = null)
    {
        if (!$this->current_session_id) {
            return true; // No active session
        }

        return $this->current_session_id === ($sessionId ?? session()->getId());
    }
}
