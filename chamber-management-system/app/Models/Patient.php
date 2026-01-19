<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'patient_code',
        'full_name',
        'phone',
        'email',
        'gender',
        'date_of_birth',
        'address',
        'referral_type',            // new
        'referred_by_patient_id',   // new
        'referred_by_text',         // new
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Generate patient code automatically.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($patient) {
            if (empty($patient->patient_code)) {
                $year = date('Y');
                $month = date('m');

                // Get the max number from existing patient codes for this month/year
                $lastNumber = (int) self::where('patient_code', 'like', "PT{$year}{$month}%")
                    ->max(DB::raw('CAST(RIGHT(patient_code, 4) AS UNSIGNED)'));

                $nextNumber = $lastNumber + 1;

                $patient->patient_code = "PT{$year}{$month}" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }

            // Set audit fields
            if (auth()->check()) {
                $patient->created_by = auth()->id();
                $patient->updated_by = auth()->id();
            }
        });


        static::updating(function ($patient) {
            if (auth()->check()) {
                $patient->updated_by = auth()->id();
            }
        });
    }

    /**
     * Age accessor.
     */
    public function getAgeAttribute()
    {
        return $this->date_of_birth->age;
    }

    /**
     * Age detail accessor (years + months).
     */
    public function getAgeDetailAttribute()
    {
        $now = now();
        $birthday = $this->date_of_birth;

        $years = $birthday->diffInYears($now);
        $months = $birthday->diffInMonths($now) % 12;

        if ($years > 0) {
            return "{$years} years" . ($months > 0 ? " {$months} months" : "");
        }

        return "{$months} months";
    }

    /**
     * Relationships.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Referral relationships.
     */
    public function referredByPatient()
    {
        return $this->belongsTo(Patient::class, 'referred_by_patient_id');
    }

    public function referredPatients()
    {
        return $this->hasMany(Patient::class, 'referred_by_patient_id');
    }

    /**
     * Accessor to get the display value for referred by.
     */
    public function getReferredByDisplayAttribute()
    {
        if ($this->referral_type === 'patient' && $this->referredByPatient) {
            return $this->referredByPatient->full_name;
        }

        return $this->referred_by_text ?? '—';
    }

    /**
     * Scope for searching patients.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('full_name', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('patient_code', 'like', "%{$search}%");
    }

    /**
     * Get the family membership of the patient.
     */
    public function familyMembership()
    {
        return $this->hasOne(PatientFamilyMember::class, 'patient_id');
    }

    /**
     * Get the family this patient belongs to.
     */
    public function family()
    {
        return $this->hasOneThrough(
            PatientFamily::class,
            PatientFamilyMember::class,
            'patient_id', // Foreign key on PatientFamilyMember table
            'id', // Foreign key on PatientFamily table
            'id', // Local key on Patient table
            'family_id' // Local key on PatientFamilyMember table
        );
    }

    /**
     * Check if patient is a family head.
     */
    public function getIsFamilyHeadAttribute()
    {
        return PatientFamily::where('head_patient_id', $this->id)->exists();
    }

    /**
     * Get the family where patient is head.
     */
    public function headFamily()
    {
        return $this->hasOne(PatientFamily::class, 'head_patient_id');
    }

    /**
     * Get the dental charts for the patient.
     */
    public function dentalCharts()
    {
        return $this->hasMany(DentalChart::class);
    }

    /**
     * Get the patient's dental chart summary.
     */
    public function getDentalChartSummaryAttribute()
    {
        $charts = $this->dentalCharts;

        if ($charts->isEmpty()) {
            return 'No dental chart recorded';
        }

        $conditions = $charts->groupBy('tooth_condition')->map->count();
        $summary = [];

        foreach ($conditions as $condition => $count) {
            $summary[] = "{$condition}: {$count} teeth";
        }

        return implode(', ', $summary);
    }
}
