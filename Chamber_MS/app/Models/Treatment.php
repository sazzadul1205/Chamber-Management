<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Treatment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'treatment_code',
        'patient_id',
        'doctor_id',
        'appointment_id',
        'treatment_type',
        'estimated_sessions',
        'completed_sessions',
        'initial_appointment_id',
        'treatment_date',
        'start_date',
        'expected_end_date',
        'actual_end_date',
        'diagnosis',
        'treatment_plan',
        'total_estimated_cost',
        'total_actual_cost',
        'discount',
        'status',
        'followup_date',
        'followup_notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'treatment_date' => 'date',
        'start_date' => 'date',
        'expected_end_date' => 'date',
        'actual_end_date' => 'date',
        'followup_date' => 'date',
        'total_estimated_cost' => 'decimal:2',
        'total_actual_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function initialAppointment()
    {
        return $this->belongsTo(Appointment::class, 'initial_appointment_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function procedures()
    {
        return $this->hasMany(TreatmentProcedure::class);
    }

    public function sessions()
    {
        return $this->hasMany(TreatmentSession::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // Scopes
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('treatment_code', 'like', "%{$search}%")
                ->orWhere('diagnosis', 'like', "%{$search}%")
                ->orWhereHas('patient', function ($patientQuery) use ($search) {
                    $patientQuery->where('full_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('doctor', function ($doctorQuery) use ($search) {
                    $doctorQuery->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('full_name', 'like', "%{$search}%");
                    });
                });
        });
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['planned', 'in_progress']);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('treatment_date', today());
    }

    // Helper Methods
    public static function statuses()
    {
        return [
            'planned' => 'Planned',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'on_hold' => 'On Hold',
        ];
    }

    public static function treatmentTypes()
    {
        return [
            'single_visit' => 'Single Visit',
            'multi_visit' => 'Multiple Visits',
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function getTreatmentTypeTextAttribute()
    {
        return self::treatmentTypes()[$this->treatment_type] ?? $this->treatment_type;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'planned' => 'info',
            'in_progress' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            'on_hold' => 'secondary',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getStatusBadgeAttribute()
    {
        return '<span class="badge bg-' . $this->status_color . '">' . $this->status_text . '</span>';
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->estimated_sessions == 0) return 0;
        return min(100, round(($this->completed_sessions / $this->estimated_sessions) * 100));
    }

    public function getProgressBarAttribute()
    {
        $percentage = $this->progress_percentage;
        $color = $this->status_color;

        return <<<HTML
        <div class="progress" style="height: 20px;">
            <div class="progress-bar bg-{$color}" role="progressbar" 
                 style="width: {$percentage}%;" 
                 aria-valuenow="{$percentage}" 
                 aria-valuemin="0" 
                 aria-valuemax="100">
                {$percentage}%
            </div>
        </div>
        HTML;
    }

    public function getSessionProgressTextAttribute()
    {
        return "{$this->completed_sessions}/{$this->estimated_sessions} sessions";
    }

    public function getFormattedEstimatedCostAttribute()
    {
        return '৳ ' . number_format($this->total_estimated_cost, 2);
    }

    public function getFormattedActualCostAttribute()
    {
        return '৳ ' . number_format($this->total_actual_cost, 2);
    }

    public function getRemainingSessionsAttribute()
    {
        return max(0, $this->estimated_sessions - $this->completed_sessions);
    }

    public function isActive()
    {
        return in_array($this->status, ['planned', 'in_progress']);
    }

    public function canAddSession()
    {
        return $this->isActive() && $this->completed_sessions < $this->estimated_sessions;
    }

    public function start()
    {
        $this->update([
            'status' => 'in_progress',
            'start_date' => now(),
            'updated_by' => auth()->id(),
        ]);
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'actual_end_date' => now(),
            'completed_sessions' => $this->estimated_sessions,
            'updated_by' => auth()->id(),
        ]);
    }

    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
            'updated_by' => auth()->id(),
        ]);
    }

    public function putOnHold()
    {
        $this->update([
            'status' => 'on_hold',
            'updated_by' => auth()->id(),
        ]);
    }

    public function resume()
    {
        $this->update([
            'status' => 'in_progress',
            'updated_by' => auth()->id(),
        ]);
    }

    public function addSession()
    {
        if ($this->canAddSession()) {
            $this->increment('completed_sessions');

            if ($this->completed_sessions >= $this->estimated_sessions) {
                $this->complete();
            }

            return true;
        }

        return false;
    }

    public static function generateTreatmentCode()
    {
        $last = self::orderByDesc('treatment_code')->first();
        $next = $last ? ((int) substr($last->treatment_code, 3)) + 1 : 1;
        return 'TRT' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    public function updateActualCost()
    {
        $proceduresCost = $this->procedures()->sum('cost');
        $this->update(['total_actual_cost' => $proceduresCost - $this->discount]);
    }
}
