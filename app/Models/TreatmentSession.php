<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentSession extends Model
{
    use HasFactory;

    // =========================
    // MASS ASSIGNMENT
    // =========================
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

    // =========================
    // TYPE CASTING
    // =========================
    protected $casts = [
        'scheduled_date'     => 'date',
        'actual_date'        => 'date',
        'next_session_date'  => 'date',
        'duration_planned'   => 'integer',
        'duration_actual'    => 'integer',
        'cost_for_session'   => 'decimal:2',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
    ];

    // =========================
    // RELATIONSHIPS
    // =========================
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
    public function allocations()
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    // =========================
    // SCOPES
    // =========================
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

    // =========================
    // STATUS HELPERS
    // =========================
    public static function statuses()
    {
        return [
            'scheduled'   => 'Scheduled',
            'in_progress' => 'In Progress',
            'completed'   => 'Completed',
            'cancelled'   => 'Cancelled',
            'postponed'   => 'Postponed',
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'scheduled'   => 'info',
            'in_progress' => 'warning',
            'completed'   => 'success',
            'cancelled'   => 'danger',
            'postponed'   => 'secondary',
        ];
        return $colors[$this->status] ?? 'secondary';
    }

    public function getStatusBadgeAttribute()
    {
        // Tailwind usage in Blade: <span class="bg-{{ $session->status_color }}-500 text-white px-2 py-1 rounded">{{ $session->status_text }}</span>
        return '<span class="badge bg-' . $this->status_color . '">' . $this->status_text . '</span>';
    }

    // =========================
    // COST & DURATION HELPERS
    // =========================
    public function getFormattedCostAttribute()
    {
        return $this->cost_for_session !== null
            ? '৳ ' . number_format((float) $this->cost_for_session, 2)
            : 'N/A';
    }

    public function getDurationPlannedTextAttribute()
    {
        return $this->duration_planned . ' minutes';
    }
    public function getDurationActualTextAttribute()
    {
        return $this->duration_actual ? $this->duration_actual . ' minutes' : 'N/A';
    }

    // =========================
    // SESSION INFO HELPERS
    // =========================
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

    // =========================
    // STATUS ACTIONS
    // =========================
    public function start()
    {
        $this->update([
            'status' => 'in_progress',
            'actual_date' => now(),
            'updated_by' => auth()->id(),
        ]);
        return true;
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'actual_date' => $this->actual_date ?? now(),
            'updated_by' => auth()->id(),
        ]);

        // Notify treatment that a session is completed
        $this->treatment->addSession();

        return true;
    }

    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
            'updated_by' => auth()->id(),
        ]);
        return true;
    }

    public function postpone($newDate = null, $notes = null)
    {
        $updateData = ['status' => 'postponed', 'updated_by' => auth()->id()];
        if ($newDate) $updateData['scheduled_date'] = $newDate;
        if ($notes) $updateData['next_session_notes'] = $notes;

        $this->update($updateData);
        return true;
    }

    public function reschedule($newDate)
    {
        $this->update([
            'status' => 'scheduled',
            'scheduled_date' => $newDate,
            'updated_by' => auth()->id(),
        ]);
        return true;
    }

    // =========================
    // PAYMENT HELPERS
    // =========================
    public function getTotalPaidAmount()
    {
        return $this->payments()->sum('amount');
    }
    public function getRemainingAmount()
    {
        return $this->cost_for_session - $this->getTotalPaidAmount();
    }
    public function getPaymentAllocatedAttribute()
    {
        return $this->allocations->sum('allocated_amount');
    }

    // =========================
    // PROCEDURES ASSOCIATED WITH SESSION
    // =========================
    public function getSessionProcedures()
    {
        return $this->treatment->procedures()->get();
    }

    public function addPayment($amount)
    {
        // Only update the paid_amount, DON'T create a payment record
        $this->paid_amount = max(0, ($this->paid_amount ?? 0) + $amount);
        $this->save();

        return true; // Just return success, don't create payment
    }

    public function deductPayment($amount)
    {
        $this->paid_amount = max(0, ($this->paid_amount ?? 0) - abs($amount));
        $this->save();

        return true;
    }

    public function getPaidAmountAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getBalanceAttribute()
    {
        return $this->cost_for_session - $this->paid_amount;
    }
}
