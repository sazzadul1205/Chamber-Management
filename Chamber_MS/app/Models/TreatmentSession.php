<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'treatment_id',
        'session_number',
        'session_title',
        'appointment_id',
        'scheduled_date',
        'actual_date',
        'chair_id',
        'status',
        'procedure_details',
        'materials_used',
        'doctor_notes',
        'assistant_notes',
        'duration_planned',
        'duration_actual',
        'cost_for_session',
        'next_session_date',
        'next_session_notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'actual_date' => 'date',
        'next_session_date' => 'date',
        'duration_planned' => 'integer',
        'duration_actual' => 'integer',
        'cost_for_session' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function chair()
    {
        return $this->belongsTo(DentalChair::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'for_treatment_session_id');
    }

    // Scopes
    public function scopeByTreatment($query, $treatmentId)
    {
        return $query->where('treatment_id', $treatmentId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->whereDate('scheduled_date', '>=', today())
            ->whereIn('status', ['scheduled', 'in_progress']);
    }

    // Helper Methods
    public static function statuses()
    {
        return [
            'scheduled' => 'Scheduled',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'postponed' => 'Postponed',
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'scheduled' => 'info',
            'in_progress' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            'postponed' => 'secondary',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getStatusBadgeAttribute()
    {
        return '<span class="badge bg-' . $this->status_color . '">' . $this->status_text . '</span>';
    }

    public function getFormattedCostAttribute()
    {
        return $this->cost_for_session ? '৳ ' . number_format($this->cost_for_session, 2) : 'N/A';
    }

    public function getDurationPlannedTextAttribute()
    {
        return $this->duration_planned . ' minutes';
    }

    public function getDurationActualTextAttribute()
    {
        return $this->duration_actual ? $this->duration_actual . ' minutes' : 'N/A';
    }

    public function getSessionInfoAttribute()
    {
        return "Session {$this->session_number}: {$this->session_title}";
    }

    public function getSessionDateAttribute()
    {
        return $this->actual_date ?? $this->scheduled_date;
    }

    public function getSessionDateTextAttribute()
    {
        return $this->session_date->format('d/m/Y');
    }

    public function getIsPastAttribute()
    {
        return $this->scheduled_date < today() && $this->status === 'scheduled';
    }

    public function getIsTodayAttribute()
    {
        return $this->scheduled_date == today();
    }

    public function getIsUpcomingAttribute()
    {
        return $this->scheduled_date > today() && $this->status === 'scheduled';
    }

    public function start()
    {
        if ($this->status === 'scheduled') {
            $this->update([
                'status' => 'in_progress',
                'actual_date' => now(),
                'updated_by' => auth()->id(),
            ]);
            return true;
        }
        return false;
    }

    public function complete()
    {
        if (in_array($this->status, ['scheduled', 'in_progress'])) {
            $this->update([
                'status' => 'completed',
                'actual_date' => $this->actual_date ?? now(),
                'updated_by' => auth()->id(),
            ]);

            // Update treatment completed sessions count
            $this->treatment->addSession();

            return true;
        }
        return false;
    }

    public function cancel()
    {
        if ($this->status !== 'completed') {
            $this->update([
                'status' => 'cancelled',
                'updated_by' => auth()->id(),
            ]);
            return true;
        }
        return false;
    }

    public function postpone($newDate = null, $notes = null)
    {
        if ($this->status === 'scheduled') {
            $updateData = [
                'status' => 'postponed',
                'updated_by' => auth()->id(),
            ];

            if ($newDate) {
                $updateData['scheduled_date'] = $newDate;
            }

            if ($notes) {
                $updateData['next_session_notes'] = $notes;
            }

            $this->update($updateData);
            return true;
        }
        return false;
    }

    public function reschedule($newDate)
    {
        if (in_array($this->status, ['scheduled', 'postponed'])) {
            $this->update([
                'status' => 'scheduled',
                'scheduled_date' => $newDate,
                'updated_by' => auth()->id(),
            ]);
            return true;
        }
        return false;
    }

    public function getSessionProcedures()
    {
        // This would typically query procedures scheduled for this session
        // For now, return treatment procedures
        return $this->treatment->procedures()->get();
    }

    public function getTotalPaidAmount()
    {
        return $this->payments()->sum('amount');
    }

    public function getRemainingAmount()
    {
        return $this->cost_for_session - $this->getTotalPaidAmount();
    }
}
