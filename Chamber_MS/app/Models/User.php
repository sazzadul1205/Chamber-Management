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

    protected $fillable = [
        'role_id',
        'full_name',
        'phone',
        'email',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'deleted_at' => 'datetime',
    ];

    // =======================
    // Relationships
    // =======================

    public function role()
    {
        return $this->belongsTo(Role::class)->withDefault([
            'name' => 'Unknown',
        ]);
    }

    public function doctor()
    {
        if (class_exists(\App\Models\Doctor::class)) {
            return $this->hasOne(\App\Models\Doctor::class);
        }
        return $this->hasOneDummy();
    }

    public function createdAppointments()
    {
        if (class_exists(\App\Models\Appointment::class)) {
            return $this->hasMany(\App\Models\Appointment::class, 'created_by');
        }
        return $this->hasManyDummy();
    }

    public function updatedAppointments()
    {
        if (class_exists(\App\Models\Appointment::class)) {
            return $this->hasMany(\App\Models\Appointment::class, 'updated_by');
        }
        return $this->hasManyDummy();
    }

    public function createdTreatments()
    {
        if (class_exists(\App\Models\Treatment::class)) {
            return $this->hasMany(\App\Models\Treatment::class, 'created_by');
        }
        return $this->hasManyDummy();
    }

    public function auditLogs()
    {
        if (class_exists(\App\Models\AuditLog::class)) {
            return $this->hasMany(\App\Models\AuditLog::class);
        }
        return $this->hasManyDummy();
    }

    // Fallback helper for missing relations
    protected function hasManyDummy()
    {
        return $this->hasMany(static::class)->whereRaw('1 = 0');
    }

    // =======================
    // Scopes
    // =======================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeByRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    // =======================
    // Helper Methods
    // =======================

    public function isSuperAdmin()
    {
        return $this->role_id === 1; // Super Admin
    }

    public function isAdmin()
    {
        return in_array($this->role_id, [1, 2]); // Admins
    }

    public function isDoctor()
    {
        return $this->role_id === 3;
    }

    public function isStaff()
    {
        return $this->role_id === 4;
    }

    public function getRoleName()
    {
        return $this->role->name ?? 'Unknown';
    }

    public function getStatusBadge()
    {
        $badges = [
            'active' => 'success',
            'inactive' => 'secondary',
            'suspended' => 'danger',
        ];

        return '<span class="badge bg-' . ($badges[$this->status] ?? 'secondary') . '">' . ucfirst($this->status) . '</span>';
    }
}
