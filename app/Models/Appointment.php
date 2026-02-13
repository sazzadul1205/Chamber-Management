<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

    public function reminders()
    {
        return $this->hasMany(AppointmentReminder::class);
    }

    // Appointment count for dashboard stats
    public static function TotalCount()
    {
        return self::count();
    }

    // Today's appointment count for dashboard stats
    public static function TodayCount()
    {
        return self::whereDate('appointment_date', today())->count();
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


    public function getDetails($id)
    {
        $appointment = Appointment::with(['patient', 'doctor.user'])->find($id);

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        return response()->json([
            'id' => $appointment->id,
            'patient' => [
                'id' => $appointment->patient->id,
                'name' => $appointment->patient->full_name,
            ],
            'doctor' => [
                'id' => $appointment->doctor->id,
                'name' => $appointment->doctor->user->full_name,
            ],
            'treatment_date' => $appointment->appointment_date->format('Y-m-d'),
            'chair_id' => $appointment->chair_id,
        ]);
    }


    // =========================
    // APPOINTMENT REMINDER METHODS
    // =========================
    public function createReminder($type, $minutesBefore = null, $customMessage = null)
    {
        // Default reminder times based on type
        $defaultTimes = [
            'sms' => 24 * 60, // 24 hours
            'email' => 48 * 60, // 48 hours
            'push' => 60, // 1 hour
        ];

        $minutes = $minutesBefore ?? $defaultTimes[$type] ?? 24 * 60;

        // Calculate scheduled time
        $scheduledAt = Carbon::parse("{$this->appointment_date} {$this->appointment_time}")
            ->subMinutes($minutes);

        // Create message
        $message = $customMessage ?? $this->generateReminderMessage($type);

        return AppointmentReminder::create([
            'appointment_id' => $this->id,
            'reminder_type' => $type,
            'message' => $message,
            'scheduled_at' => $scheduledAt,
            'status' => 'pending'
        ]);
    }

    // Generate reminder message based on type
    private function generateReminderMessage($type)
    {
        $doctorName = $this->doctor->user->name ?? 'Doctor';
        $patientName = $this->patient->name ?? 'Patient';
        $date = Carbon::parse($this->appointment_date)->format('M d, Y');
        $time = Carbon::parse($this->appointment_time)->format('h:i A');
        $typeName = ucfirst($this->appointment_type);

        $baseMessage = "Dear {$patientName}, this is a reminder for your {$typeName} appointment with Dr. {$doctorName} on {$date} at {$time}.";

        if ($type === 'sms') {
            return substr($baseMessage . " Location: Dental Clinic. Reply YES to confirm.", 0, 160);
        }

        if ($type === 'email') {
            return view('emails.appointment-reminder', [
                'appointment' => $this,
                'doctorName' => $doctorName,
                'patientName' => $patientName,
                'date' => $date,
                'time' => $time,
                'typeName' => $typeName
            ])->render();
        }

        return $baseMessage;
    }

    // Send due reminders
    public function sendReminders()
    {
        $pendingReminders = $this->reminders()->pending()->due()->get();

        foreach ($pendingReminders as $reminder) {
            try {
                switch ($reminder->reminder_type) {
                    case 'sms':
                        $this->sendSmsReminder($reminder);
                        break;
                    case 'email':
                        $this->sendEmailReminder($reminder);
                        break;
                    case 'push':
                        $this->sendPushReminder($reminder);
                        break;
                }

                $reminder->markAsSent(['sent_via' => 'system']);
            } catch (\Exception $e) {
                $reminder->markAsFailed($e->getMessage());
                Log::error("Failed to send reminder {$reminder->id}: " . $e->getMessage());
            }
        }
    }

    // SMS sending logic (using a hypothetical SMS service)
    private function sendSmsReminder($reminder)
    {
        $patientPhone = $this->patient->phone;
        if (!$patientPhone) {
            throw new \Exception("Patient phone number not available");
        }

        // Use your SMS service (Twilio, etc.)
        // Example with Twilio:
        // $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        // $twilio->messages->create(
        //     $patientPhone,
        //     ['from' => config('services.twilio.from'), 'body' => $reminder->message]
        // );

        // For now, just log
        Log::info("SMS sent to {$patientPhone}: {$reminder->message}");
    }

    // Email sending logic (using Laravel's Mail)
    private function sendEmailReminder($reminder)
    {
        $patientEmail = $this->patient->email;
        if (!$patientEmail) {
            throw new \Exception("Patient email not available");
        }

        // Check if patient has email notifications enabled (optional)
        if ($this->patient->email_notifications === false) {
            throw new \Exception("Patient has disabled email notifications");
        }

        try {
            Mail::to($patientEmail)->send(new \App\Mail\AppointmentReminderMail($this, $reminder->message));

            // Log the email sent
            Log::info("Appointment reminder email sent to {$patientEmail} for appointment #{$this->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send email reminder for appointment #{$this->id}: " . $e->getMessage());
            throw $e; // Re-throw to handle in parent method
        }
    }

    // Push notification sending logic
    private function sendPushReminder($reminder)
    {
        // Implement push notification logic
        // This could be for a mobile app
        Log::info("Push notification sent for appointment {$this->id}");
    }

    // Get upcoming reminders
    public function getUpcomingReminders()
    {
        return $this->reminders()
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at')
            ->get();
    }

    // Get sent reminders
    public function getSentReminders()
    {
        return $this->reminders()
            ->sent()
            ->orderBy('sent_at', 'desc')
            ->get();
    }
}
