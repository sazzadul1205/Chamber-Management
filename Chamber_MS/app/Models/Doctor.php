<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'doctor_code',
        'specialization',
        'qualification',
        'consultation_fee',
        'commission_percent',
        'status',
    ];

    protected $casts = [
        'consultation_fee' => 'decimal:2',
        'commission_percent' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ==========================================================
     | RELATIONSHIPS (PURE — NEVER RETURN COLLECTIONS HERE)
     ========================================================== */

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'full_name' => 'Demo Doctor',
            'phone'     => '0000000000',
            'email'     => 'demo@example.com',
        ]);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    public function treatmentSessions()
    {
        return $this->hasManyThrough(
            TreatmentSession::class,
            Treatment::class,
            'doctor_id',
            'treatment_id'
        );
    }

    /* ==========================================================
     | SAFE ACCESSORS (YOUR 2 RULES IMPLEMENTED HERE)
     ========================================================== */

    public function getSafeAppointmentsAttribute(): ?Collection
    {
        // Rule 2: table missing → demo data
        if (!Schema::hasTable('appointments')) {
            return collect([
                (object) [
                    'id' => 0,
                    'appointment_date' => now(),
                    'status' => 'scheduled',
                    'patient' => (object) ['full_name' => 'Demo Patient'],
                ],
            ]);
        }

        // Rule 1: table exists, no data → null
        if (!$this->appointments()->exists()) {
            return null;
        }

        return $this->appointments;
    }

    public function getSafeTreatmentsAttribute(): ?Collection
    {
        if (!Schema::hasTable('treatments')) {
            return collect([
                (object) [
                    'id' => 0,
                    'description' => 'Demo Treatment',
                    'status' => 'pending',
                    'patient' => (object) ['full_name' => 'Demo Patient'],
                ],
            ]);
        }

        if (!$this->treatments()->exists()) {
            return null;
        }

        return $this->treatments;
    }

    public function getSafeTreatmentSessionsAttribute(): ?Collection
    {
        if (
            !Schema::hasTable('treatment_sessions') ||
            !Schema::hasTable('treatments')
        ) {
            return collect([
                (object) [
                    'id' => 0,
                    'session_date' => now(),
                    'notes' => 'Demo Session',
                ],
            ]);
        }

        if (!$this->treatmentSessions()->exists()) {
            return null;
        }

        return $this->treatmentSessions;
    }

    /* ==========================================================
     | SCOPES
     ========================================================== */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOnLeave($query)
    {
        return $query->where('status', 'on_leave');
    }

    public function scopeBySpecialization($query, $specialization)
    {
        return $query->where('specialization', 'like', "%{$specialization}%");
    }

    /* ==========================================================
     | HELPERS (SAFE — NO DB SIDE EFFECTS)
     ========================================================== */

    public function getFullNameAttribute()
    {
        return $this->user->full_name ?? 'Demo Doctor';
    }

    public function getPhoneAttribute()
    {
        return $this->user->phone ?? '0000000000';
    }

    public function getEmailAttribute()
    {
        return $this->user->email ?? 'demo@example.com';
    }

    public function getFormattedConsultationFee()
    {
        return '৳ ' . number_format($this->consultation_fee ?? 0, 2);
    }

    public function getFormattedCommission()
    {
        return ($this->commission_percent ?? 0) . '%';
    }

    public function getTotalAppointments()
    {
        return Schema::hasTable('appointments')
            ? $this->appointments()->count()
            : 0;
    }

    public function getTotalPatients()
    {
        return Schema::hasTable('treatments')
            ? $this->treatments()->distinct('patient_id')->count('patient_id')
            : 0;
    }

    public function getUpcomingAppointments()
    {
        return Schema::hasTable('appointments')
            ? $this->appointments()
            ->whereDate('appointment_date', '>=', now())
            ->where('status', 'scheduled')
            ->count()
            : 0;
    }
}
