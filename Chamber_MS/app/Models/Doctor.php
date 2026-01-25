<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    // =========================
    // FILLABLES
    // =========================
    protected $fillable = [
        'user_id',
        'doctor_code',
        'specialization',
        'qualification',
        'consultation_fee',
        'commission_percent',
        'status',
    ];

    // =========================
    // CASTS
    // =========================
    protected $casts = [
        'consultation_fee' => 'decimal:2',
        'commission_percent' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // =========================
    // RELATIONSHIPS
    // =========================

    /** Doctor belongs to a User */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'full_name' => 'Demo Doctor',
            'phone'     => '0000000000',
            'email'     => 'demo@example.com',
        ]);
    }

    /** Doctor has many appointments */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /** Doctor has many treatments */
    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    /** Doctor has many treatment sessions via treatments */
    public function treatmentSessions()
    {
        return $this->hasManyThrough(
            TreatmentSession::class,
            Treatment::class,
            'doctor_id',    // Foreign key on treatments table
            'treatment_id'  // Foreign key on treatment_sessions table
        );
    }

    // =========================
    // SCOPES
    // =========================

    /** Active doctors */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /** Doctors on leave */
    public function scopeOnLeave($query)
    {
        return $query->where('status', 'on_leave');
    }

    /** Filter by specialization */
    public function scopeBySpecialization($query, $specialization)
    {
        return $query->where('specialization', 'like', "%{$specialization}%");
    }

    // =========================
    // ACCESSORS / HELPERS
    // =========================

    /** Full name of the doctor */
    public function getFullNameAttribute()
    {
        return $this->user->full_name ?? 'Demo Doctor';
    }

    /** Phone number */
    public function getPhoneAttribute()
    {
        return $this->user->phone ?? '0000000000';
    }

    /** Email */
    public function getEmailAttribute()
    {
        return $this->user->email ?? 'demo@example.com';
    }

    /** Formatted consultation fee with currency */
    public function getFormattedConsultationFeeAttribute()
    {
        return '৳ ' . number_format($this->consultation_fee ?? 0, 2);
    }

    /** Commission percentage formatted */
    public function getFormattedCommissionAttribute()
    {
        return ($this->commission_percent ?? 0) . '%';
    }

    /** Total number of appointments */
    public function getTotalAppointmentsAttribute()
    {
        return $this->appointments()->count();
    }

    /** Total unique patients treated */
    public function getTotalPatientsAttribute()
    {
        return $this->treatments()->distinct('patient_id')->count('patient_id');
    }

    /** Upcoming scheduled appointments count */
    public function getUpcomingAppointmentsAttribute()
    {
        return $this->appointments()
            ->whereDate('appointment_date', '>=', now())
            ->where('status', 'scheduled')
            ->count();
    }

    // =========================
    // TAILWIND HELPER EXAMPLES
    // =========================

    /**
     * Returns Tailwind badge class based on doctor status
     * e.g., 'bg-green-500' for active, 'bg-yellow-400' for on_leave
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'active' => 'bg-green-500 text-white',
            'on_leave' => 'bg-yellow-400 text-black',
            default => 'bg-gray-300 text-black',
        };
    }
}
