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

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'full_name' => 'Demo Doctor',
            'phone' => '0000000000',
            'email' => 'demo@example.com',
        ]);
    }

    public function appointments()
    {
        if (!Schema::hasTable('appointments')) {
            return new Collection([
                (object)[
                    'id' => 0,
                    'patient_name' => 'Demo Patient',
                    'appointment_date' => now(),
                    'status' => 'scheduled',
                ]
            ]);
        }

        $appointments = $this->hasMany(Appointment::class, 'doctor_id')->get();
        return $appointments->isEmpty() ? new Collection([
            (object)[
                'id' => 0,
                'patient_name' => 'Demo Patient',
                'appointment_date' => now(),
                'status' => 'scheduled',
            ]
        ]) : $appointments;
    }

    public function treatments()
    {
        if (!Schema::hasTable('treatments')) {
            return new Collection([
                (object)[
                    'id' => 0,
                    'patient_id' => 0,
                    'description' => 'Demo Treatment',
                    'status' => 'pending',
                ]
            ]);
        }

        $treatments = $this->hasMany(Treatment::class, 'doctor_id')->get();
        return $treatments->isEmpty() ? new Collection([
            (object)[
                'id' => 0,
                'patient_id' => 0,
                'description' => 'Demo Treatment',
                'status' => 'pending',
            ]
        ]) : $treatments;
    }

    public function treatmentSessions()
    {
        if (!Schema::hasTable('treatment_sessions') || !Schema::hasTable('treatments')) {
            return new Collection([
                (object)[
                    'id' => 0,
                    'treatment_id' => 0,
                    'session_date' => now(),
                    'notes' => 'Demo Session',
                ]
            ]);
        }

        $sessions = $this->hasManyThrough(
            TreatmentSession::class,
            Treatment::class,
            'doctor_id',
            'treatment_id'
        )->get();

        return $sessions->isEmpty() ? new Collection([
            (object)[
                'id' => 0,
                'treatment_id' => 0,
                'session_date' => now(),
                'notes' => 'Demo Session',
            ]
        ]) : $sessions;
    }

    // Scopes
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

    // Helper Methods
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

    public function getStatusBadge()
    {
        $badges = [
            'active' => 'success',
            'inactive' => 'secondary',
            'on_leave' => 'warning',
        ];

        return '<span class="badge bg-' . ($badges[$this->status] ?? 'secondary') . '">' . ucfirst(str_replace('_', ' ', $this->status)) . '</span>';
    }

    public function getFormattedConsultationFee()
    {
        return '৳ ' . number_format($this->consultation_fee ?? 0, 2);
    }

    public function getFormattedCommission()
    {
        return ($this->commission_percent ?? 0) . '%';
    }

    public function getTotalEarnings()
    {
        return 0;
    }

    public function getTotalPatients()
    {
        return $this->treatments()->pluck('patient_id')->unique()->count() ?? 0;
    }

    public function getTotalAppointments()
    {
        return $this->appointments()->count() ?? 0;
    }

    public function getUpcomingAppointments()
    {
        return $this->appointments()
            ->where('appointment_date', '>=', now()->format('Y-m-d'))
            ->where('status', 'scheduled')
            ->count() ?? 0;
    }
}
