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
        'full_name', 'phone', 'email', 'password', 'role_id', 'status',
        'last_login_at', 'last_login_device_id', 'current_session_id', 'blood_group',
    ];

    // =======================
    // Hidden Fields
    // =======================
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // =======================
    // Attribute Casting
    // =======================
    protected $casts = [
        'last_login_at' => 'datetime',
        'email_verified_at' => 'datetime',
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
     * Filter by role
     */
    public function scopeByRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
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

    /**
     * Get role name
     */
    public function getRoleName()
    {
        return $this->role->name ?? 'Unknown';
    }

    /**
     * Get Tailwind-friendly status badge
     */
    public function getStatusBadge()
    {
        $badges = [
            'active' => 'green',
            'inactive' => 'gray',
            'suspended' => 'red',
        ];

        $color = $badges[$this->status] ?? 'gray';

        return '<span class="bg-'.$color.'-100 text-'.$color.'-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">'
            .ucfirst($this->status)
            .'</span>';
    }
}
