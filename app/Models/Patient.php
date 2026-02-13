<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    // =========================
    // MASS ASSIGNABLE FIELDS
    // =========================
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

    // =========================
    // ATTRIBUTE CASTS
    // =========================
    protected $casts = [
        'date_of_birth' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // =========================
    // RELATIONSHIPS
    // =========================

    // User who created this patient
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // User who last updated this patient
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Referring patient
    public function referrer()
    {
        return $this->belongsTo(Patient::class, 'referred_by');
    }

    // Patients referred by this patient
    public function referrals()
    {
        return $this->hasMany(Patient::class, 'referred_by');
    }

    // Financial and clinical relations
    public function receipts()
    {
        return $this->hasMany(Receipt::class);
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
    public function families()
    {
        return $this->belongsToMany(
            PatientFamily::class,
            'patient_family_members',
            'patient_id',
            'family_id'
        )->withPivot('relationship', 'is_head')->withTimestamps();
    }

    // =========================
    // SCOPES
    // =========================
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

    // =========================
    // HELPER METHODS
    // =========================

    // Age calculation
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function getAgeTextAttribute()
    {
        return $this->age ? $this->age . ' years' : 'N/A';
    }

    // Human-readable gender
    public function getGenderTextAttribute()
    {
        return match ($this->gender) {
            'male' => 'Male',
            'female' => 'Female',
            'other' => 'Other',
            default => 'N/A'
        };
    }

    // Tailwind-ready status badge
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'active' => '<span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-green-500 rounded">Active</span>',
            'inactive' => '<span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-gray-400 rounded">Inactive</span>',
            'deceased' => '<span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-black rounded">Deceased</span>',
            default => '<span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-gray-300 rounded">N/A</span>',
        };
    }

    // Total completed visits
    public function getTotalVisitsAttribute()
    {
        return $this->appointments()->where('status', 'completed')->count();
    }

    // Total Patient Count
    public static function totalCount(): int
    {
        return static::count();
    }

    // Today's patient count
    public static function todayCount(): int
    {
        return static::whereDate('created_at', today())->count();
    }


    // Total unpaid invoice balance
    public function getTotalPendingAmountAttribute()
    {
        return $this->invoices()->where('status', '!=', 'paid')->sum('balance_amount');
    }

    // Generate unique patient code
    public static function generatePatientCode(): string
    {
        $last = self::orderByDesc('patient_code')->first();
        $next = $last ? ((int) substr($last->patient_code, 3)) + 1 : 1;
        return 'PAT' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    public function referredPatients()
    {
        return $this->hasMany(Patient::class, 'referred_by')->with('appointments');
    }

    // Add this method to get referral stats
    public function getReferralStatsAttribute()
    {
        $referred = $this->referredPatients();
        $completed = $referred->whereHas('appointments', function ($q) {
            $q->where('status', 'completed');
        })->count();

        $active = $referred->where('status', 'active')->count();
        $totalValue = $referred->withSum('invoices', 'total_amount')->get()->sum('invoices_sum_total_amount') ?? 0;

        return [
            'total_referred' => $referred->count(),
            'completed_visits' => $completed,
            'active_patients' => $active,
            'total_value' => $totalValue,
        ];
    }
}
