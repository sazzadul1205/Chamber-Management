<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    // =========================
    // MASS ASSIGNABLE FIELDS
    // =========================
    protected $fillable = [
        'appointment_code',
        'patient_id',
        'doctor_id',
        'chair_id',
        'appointment_type',
        'schedule_type',
        'appointment_date',
        'appointment_time',
        'expected_duration',
        'queue_no',
        'status',
        'priority',
        'chief_complaint',
        'notes',
        'reason_cancellation',
        'checked_in_time',
        'started_time',
        'completed_time',
        'created_by',
        'updated_by',
    ];

    // =========================
    // CASTS
    // =========================
    protected $casts = [
        'appointment_date' => 'date',
        'checked_in_time' => 'datetime',
        'started_time' => 'datetime',
        'completed_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // =========================
    // RELATIONSHIPS
    // =========================
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function chair()
    {
        return $this->belongsTo(DentalChair::class, 'chair_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function treatment()
    {
        return $this->hasOne(Treatment::class);
    }

    // =========================
    // SCOPES
    // =========================
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('appointment_code', 'like', "%{$search}%")
                ->orWhere('chief_complaint', 'like', "%{$search}%")
                ->orWhereHas(
                    'patient',
                    fn($patientQuery) =>
                    $patientQuery->where('full_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                )
                ->orWhereHas(
                    'doctor',
                    fn($doctorQuery) =>
                    $doctorQuery->whereHas(
                        'user',
                        fn($userQuery) =>
                        $userQuery->where('full_name', 'like', "%{$search}%")
                    )
                );
        });
    }

    public function scopeQueueToday($query)
    {
        return $query
            ->whereDate('appointment_date', today())
            ->whereIn('status', ['checked_in', 'in_progress'])
            ->orderByRaw("
            CASE priority
                WHEN 'high' THEN 1
                WHEN 'urgent' THEN 2
                ELSE 3
            END
        ")
            ->orderBy('queue_no');
    }


    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->whereDate('appointment_date', '>=', today())
            ->whereIn('status', ['scheduled', 'checked_in']);
    }

    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('appointment_date', $date);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['scheduled', 'checked_in', 'in_progress']);
    }

    // =========================
    // HELPER DATA ARRAYS
    // =========================
    public static function appointmentTypes()
    {
        return [
            'consultation' => 'Consultation',
            'treatment'    => 'Treatment',
            'followup'     => 'Follow-up',
            'emergency'    => 'Emergency',
            'checkup'      => 'Checkup',
        ];
    }

    public static function statuses()
    {
        return [
            'scheduled'   => 'Scheduled',
            'checked_in'  => 'Checked In',
            'in_progress' => 'In Progress',
            'completed'   => 'Completed',
            'cancelled'   => 'Cancelled',
            'no_show'     => 'No Show',
        ];
    }

    public static function priorities()
    {
        return [
            'normal' => 'Normal',
            'urgent' => 'Urgent',
            'high'   => 'High',
        ];
    }

    // =========================
    // ATTRIBUTE ACCESSORS
    // =========================
    public function getAppointmentTypeTextAttribute()
    {
        return self::appointmentTypes()[$this->appointment_type] ?? $this->appointment_type;
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function getPriorityTextAttribute()
    {
        return self::priorities()[$this->priority] ?? $this->priority;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'scheduled'   => 'info',
            'checked_in'  => 'primary',
            'in_progress' => 'warning',
            'completed'   => 'success',
            'cancelled'   => 'danger',
            'no_show'     => 'secondary',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getPriorityColorAttribute()
    {
        $colors = [
            'normal' => 'info',
            'urgent' => 'warning',
            'high'   => 'danger',
        ];

        return $colors[$this->priority] ?? 'info';
    }

    public function getStatusBadgeAttribute()
    {
        // Tailwind or Bootstrap badge can be used in views
        return '<span class="badge bg-' . $this->status_color . '">' . $this->status_text . '</span>';
    }

    public function getPriorityBadgeAttribute()
    {
        return '<span class="badge bg-' . $this->priority_color . '">' . $this->priority_text . '</span>';
    }

    public function getDateTimeAttribute()
    {
        return $this->appointment_date->format('d/m/Y') . ' ' . date('h:i A', strtotime($this->appointment_time));
    }

    public function getEndTimeAttribute()
    {
        $endTime = strtotime($this->appointment_time) + ($this->expected_duration * 60);
        return date('h:i A', $endTime);
    }

    public function getDurationTextAttribute()
    {
        return $this->expected_duration . ' minutes';
    }

    // =========================
    // STATUS CHECK HELPERS
    // =========================
    public function isUpcoming()
    {
        return in_array($this->status, ['scheduled', 'checked_in']) &&
            $this->appointment_date >= now()->startOfDay();
    }

    public function isPast()
    {
        return $this->appointment_date < now()->startOfDay() ||
            in_array($this->status, ['completed', 'cancelled', 'no_show']);
    }

    // =========================
    // APPOINTMENT CODE GENERATION
    // =========================
    public static function generateAppointmentCode()
    {
        $last = self::orderByDesc('appointment_code')->first();
        $next = $last ? ((int) substr($last->appointment_code, 3)) + 1 : 1;
        return 'APT' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    // =========================
    // STATUS MANAGEMENT METHODS
    // =========================
    public function checkIn()
    {
        if ($this->checked_in_time) {
            return;
        }

        $this->update([
            'status' => 'checked_in',
            'checked_in_time' => now(),
            'queue_no' => self::nextQueueNumber(),
            'updated_by' => auth()->id(),
        ]);
    }


    public function start()
    {
        $this->update([
            'status' => 'in_progress',
            'started_time' => now(),
            'updated_by' => auth()->id(),
        ]);
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_time' => now(),
            'updated_by' => auth()->id(),
        ]);
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'reason_cancellation' => $reason,
            'updated_by' => auth()->id(),
        ]);
    }

    // 
    public static function nextQueueNumber()
    {
        return (int) self::whereDate('appointment_date', today())
            ->max('queue_no') + 1;
    }
}
