<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Appointment extends Model
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
        'chair_id',
        'appointment_type',
        'appointment_date',
        'appointment_time',
        'queue_no',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime:H:i:s',
    ];

    /**
     * Automatically set audit fields.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($appointment) {
            if (auth()->check()) {
                $appointment->created_by = auth()->id();
                $appointment->updated_by = auth()->id();
            }

            // Generate queue number for FIFO appointments
            if ($appointment->appointment_type === 'fifo' && empty($appointment->queue_no)) {
                $lastQueue = self::where('appointment_date', $appointment->appointment_date)
                    ->where('appointment_type', 'fifo')
                    ->max('queue_no');
                $appointment->queue_no = $lastQueue ? $lastQueue + 1 : 1;
            }
        });

        static::updating(function ($appointment) {
            if (auth()->check()) {
                $appointment->updated_by = auth()->id();
            }

            // Update chair status if appointment is in progress or completed
            if ($appointment->isDirty('status')) {
                $chair = $appointment->chair;
                if ($chair && in_array($appointment->status, ['in_progress', 'checked_in'])) {
                    $chair->update(['status' => 'occupied']);
                } elseif ($chair && in_array($appointment->status, ['completed', 'cancelled', 'no_show'])) {
                    // Check if chair has other active appointments
                    $hasOtherActive = self::where('chair_id', $chair->id)
                        ->where('appointment_date', $appointment->appointment_date)
                        ->whereIn('status', ['checked_in', 'in_progress'])
                        ->where('id', '!=', $appointment->id)
                        ->exists();

                    if (!$hasOtherActive) {
                        $chair->update(['status' => 'available']);
                    }
                }
            }
        });
    }

    /**
     * Get the patient associated with the appointment.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor associated with the appointment.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the dental chair associated with the appointment.
     */
    public function chair()
    {
        return $this->belongsTo(DentalChair::class, 'chair_id');
    }

    /**
     * Get the user who created the appointment.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the appointment.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the appointment date and time combined.
     */
    public function getDateTimeAttribute()
    {
        if ($this->appointment_time) {
            return Carbon::parse($this->appointment_date->format('Y-m-d') . ' ' . $this->appointment_time);
        }
        return $this->appointment_date;
    }

    /**
     * Check if appointment is scheduled for today.
     */
    public function getIsTodayAttribute()
    {
        return $this->appointment_date->isToday();
    }

    /**
     * Check if appointment is upcoming.
     */
    public function getIsUpcomingAttribute()
    {
        return $this->appointment_date >= today() && $this->status === 'scheduled';
    }

    /**
     * Check if appointment is in progress.
     */
    public function getIsInProgressAttribute()
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if appointment is completed.
     */
    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed';
    }

    /**
     * Get appointment duration (default 30 minutes).
     */
    public function getDurationAttribute()
    {
        return 30; // minutes
    }

    /**
     * Get appointment end time.
     */
    public function getEndTimeAttribute()
    {
        if ($this->appointment_time) {
            return Carbon::parse($this->appointment_time)->addMinutes($this->duration);
        }
        return null;
    }

    /**
     * Get formatted appointment time.
     */
    public function getFormattedTimeAttribute()
    {
        if ($this->appointment_time) {
            return Carbon::parse($this->appointment_time)->format('h:i A');
        }
        return 'FIFO - Queue #' . $this->queue_no;
    }

    /**
     * Get status color for display.
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'scheduled' => 'bg-blue-100 text-blue-800',
            'checked_in' => 'bg-yellow-100 text-yellow-800',
            'in_progress' => 'bg-purple-100 text-purple-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'no_show' => 'bg-gray-100 text-gray-800',
        ];

        return $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Scope for today's appointments.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', today());
    }

    /**
     * Scope for upcoming appointments.
     */
    public function scopeUpcoming($query, $days = 7)
    {
        return $query->whereDate('appointment_date', '>=', today())
            ->whereDate('appointment_date', '<=', today()->addDays($days))
            ->where('status', 'scheduled');
    }

    /**
     * Scope for appointments by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for appointments by doctor.
     */
    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    /**
     * Scope for appointments by chair.
     */
    public function scopeByChair($query, $chairId)
    {
        return $query->where('chair_id', $chairId);
    }

    /**
     * Scope for searching appointments.
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('patient', function ($q) use ($search) {
            $q->where('full_name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('patient_code', 'like', "%{$search}%");
        })
            ->orWhereHas('doctor.user', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            })
            ->orWhere('queue_no', 'like', "%{$search}%");
    }

    /**
     * Get the treatment associated with the appointment.
     */
    public function treatment()
    {
        return $this->hasOne(Treatment::class);
    }
}
