<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_code',
        'full_name',
        'gender',
        'date_of_birth',
        'phone',
        'email',
        'address',
        'emergency_contact',
        'referred_by',
        'status',
        'medical_history',
        'allergies',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function referrer()
    {
        return $this->belongsTo(Patient::class, 'referred_by');
    }

    public function referrals()
    {
        return $this->hasMany(Patient::class, 'referred_by');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    public function dentalCharts()
    {
        return $this->hasMany(DentalChart::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('patient_code', 'like', "%{$search}%")
                ->orWhere('full_name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Helper Methods
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function getAgeTextAttribute()
    {
        if (!$this->date_of_birth) return 'N/A';

        $age = $this->age;
        return $age . ' years';
    }

    public function getGenderTextAttribute()
    {
        return match ($this->gender) {
            'male' => 'Male',
            'female' => 'Female',
            'other' => 'Other',
            default => 'N/A'
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'active' => '<span class="badge bg-success">Active</span>',
            'inactive' => '<span class="badge bg-secondary">Inactive</span>',
            'deceased' => '<span class="badge bg-dark">Deceased</span>',
            default => '<span class="badge bg-secondary">N/A</span>'
        };
    }

    public function getTotalVisitsAttribute()
    {
        return $this->appointments()->where('status', 'completed')->count();
    }

    public function getTotalPendingAmountAttribute()
    {
        return $this->invoices()->where('status', '!=', 'paid')->sum('balance_amount');
    }

    public static function generatePatientCode()
    {
        $last = self::orderByDesc('patient_code')->first();
        $next = $last ? ((int) substr($last->patient_code, 3)) + 1 : 1;
        return 'PAT' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}
