<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    // =========================
    // RELATIONSHIPS
    // =========================

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'full_name' => 'Demo Doctor',
            'phone'     => '0000000000',
            'email'     => 'demo@example.com',
        ]);
    }

    /** Doctor has many schedules */
    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    /** Doctor has many leave requests */
    public function leaves()
    {
        return $this->hasMany(DoctorLeave::class);
    }

    /** Active schedules only */
    public function activeSchedules()
    {
        return $this->hasMany(DoctorSchedule::class)->where('is_active', true);
    }

    /** Approved leaves only */
    public function approvedLeaves()
    {
        return $this->hasMany(DoctorLeave::class)->where('status', 'approved');
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

    // =========================
    // SCOPES
    // =========================

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

    /** Scope for doctors available on specific day */
    public function scopeAvailableOnDay($query, $dayOfWeek)
    {
        return $query->whereHas('activeSchedules', function ($q) use ($dayOfWeek) {
            $q->where('day_of_week', strtolower($dayOfWeek));
        });
    }

    /** Scope for doctors available on specific date */
    public function scopeAvailableOnDate($query, $date)
    {
        $dayOfWeek = strtolower(date('l', strtotime($date)));

        return $query->whereHas('activeSchedules', function ($q) use ($dayOfWeek) {
            $q->where('day_of_week', $dayOfWeek);
        })->whereDoesntHave('approvedLeaves', function ($q) use ($date) {
            $q->whereDate('leave_date', $date);
        });
    }

    // =========================
    // ACCESSORS / HELPERS
    // =========================

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

    public function getFormattedConsultationFeeAttribute()
    {
        return '৳ ' . number_format($this->consultation_fee ?? 0, 2);
    }

    public function getFormattedCommissionAttribute()
    {
        return ($this->commission_percent ?? 0) . '%';
    }

    public function getTotalAppointmentsAttribute()
    {
        return $this->appointments()->count();
    }

    public function getTotalPatientsAttribute()
    {
        return $this->treatments()->distinct('patient_id')->count('patient_id');
    }

    public function getUpcomingAppointmentsAttribute()
    {
        return $this->appointments()
            ->whereDate('appointment_date', '>=', now())
            ->where('status', 'scheduled')
            ->count();
    }

    /** Get working days as array */
    public function getWorkingDaysAttribute()
    {
        return $this->activeSchedules->pluck('day_of_week')->toArray();
    }

    /** Get working hours for a specific day */
    public function getWorkingHours($dayOfWeek)
    {
        $schedule = $this->schedules()->where('day_of_week', strtolower($dayOfWeek))->first();

        if (!$schedule || !$schedule->is_active) {
            return null;
        }

        return [
            'start_time' => $schedule->start_time,
            'end_time' => $schedule->end_time,
            'max_appointments' => $schedule->max_appointments,
            'slot_duration' => $schedule->slot_duration,
        ];
    }

    /** Check if doctor is available on specific date and time */
    public function isAvailable($date, $time = null)
    {
        $dayOfWeek = strtolower(date('l', strtotime($date)));

        // Check if doctor has schedule for this day
        $schedule = $this->schedules()->where('day_of_week', $dayOfWeek)->first();
        if (!$schedule || !$schedule->is_active) {
            return false;
        }

        // Check if doctor is on leave
        $isOnLeave = $this->leaves()
            ->whereDate('leave_date', $date)
            ->where('status', 'approved')
            ->exists();

        if ($isOnLeave) {
            return false;
        }

        // Check specific time if provided
        if ($time) {
            $appointmentTime = strtotime($time);
            $startTime = strtotime($schedule->start_time);
            $endTime = strtotime($schedule->end_time);

            if ($appointmentTime < $startTime || $appointmentTime > $endTime) {
                return false;
            }

            // Check appointment count for the day
            $appointmentsCount = $this->appointments()
                ->whereDate('appointment_date', $date)
                ->whereIn('status', ['scheduled', 'checked_in'])
                ->count();

            if ($appointmentsCount >= $schedule->max_appointments) {
                return false;
            }
        }

        return true;
    }

    /** Get available time slots for a specific date */
    public function getAvailableSlots($date)
    {
        if (!$this->isAvailable($date)) {
            return [];
        }

        $dayOfWeek = strtolower(date('l', strtotime($date)));
        $schedule = $this->schedules()->where('day_of_week', $dayOfWeek)->first();

        $startTime = strtotime($schedule->start_time);
        $endTime = strtotime($schedule->end_time);
        $slotDuration = $schedule->slot_duration * 60; // convert to seconds

        $slots = [];
        $currentTime = $startTime;

        while ($currentTime + $slotDuration <= $endTime) {
            $slotTime = date('H:i', $currentTime);

            // Check if slot is booked
            $isBooked = $this->appointments()
                ->whereDate('appointment_date', $date)
                ->whereTime('appointment_time', $slotTime)
                ->whereIn('status', ['scheduled', 'checked_in'])
                ->exists();

            if (!$isBooked) {
                $slots[] = $slotTime;
            }

            $currentTime += $slotDuration;
        }

        return $slots;
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'active' => 'bg-green-500 text-white',
            'on_leave' => 'bg-yellow-400 text-black',
            default => 'bg-gray-300 text-black',
        };
    }
}
