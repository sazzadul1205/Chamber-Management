<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Treatment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_id',
        'diagnosis',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * Automatically set audit fields.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($treatment) {
            if (auth()->check()) {
                $treatment->created_by = auth()->id();
                $treatment->updated_by = auth()->id();
            }
        });

        static::updating(function ($treatment) {
            if (auth()->check()) {
                $treatment->updated_by = auth()->id();
            }
        });
    }

    /**
     * Get the patient associated with the treatment.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor associated with the treatment.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the appointment associated with the treatment.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the procedures for this treatment.
     */
    public function procedures()
    {
        return $this->hasMany(TreatmentProcedure::class);
    }

    /**
     * Get the prescriptions for this treatment.
     */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    /**
     * Get the medical files for this treatment.
     */
    public function medicalFiles()
    {
        return $this->hasMany(MedicalFile::class);
    }

    /**
     * Get the user who created the treatment.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the treatment.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get total cost of treatment procedures.
     */
    public function getTotalCostAttribute()
    {
        return $this->procedures->sum('price');
    }

    /**
     * Get completed procedures count.
     */
    public function getCompletedProceduresCountAttribute()
    {
        return $this->procedures->where('status', 'done')->count();
    }

    /**
     * Get pending procedures count.
     */
    public function getPendingProceduresCountAttribute()
    {
        return $this->procedures->where('status', 'planned')->count();
    }

    /**
     * Get status color for display.
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'ongoing' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
        ];
        
        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Check if treatment is ongoing.
     */
    public function getIsOngoingAttribute()
    {
        return $this->status === 'ongoing';
    }

    /**
     * Check if treatment is completed.
     */
    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed';
    }

    /**
     * Scope for ongoing treatments.
     */
    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    /**
     * Scope for completed treatments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for treatments by patient.
     */
    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    /**
     * Scope for treatments by doctor.
     */
    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    /**
     * Scope for searching treatments.
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('patient', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('patient_code', 'like', "%{$search}%");
            })
            ->orWhereHas('doctor.user', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%");
            })
            ->orWhere('diagnosis', 'like', "%{$search}%");
    }
}