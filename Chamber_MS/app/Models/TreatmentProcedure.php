<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentProcedure extends Model
{
    use HasFactory;

    // =========================
    // MASS ASSIGNABLE FIELDS
    // =========================
    protected $fillable = [
        'treatment_id',
        'procedure_code',
        'procedure_name',
        'tooth_number',
        'surface',
        'cost',
        'duration',
        'status',
        'notes',
        'completed_at',
        'completed_by',
    ];

    // =========================
    // CASTS
    // =========================
    protected $casts = [
        'cost' => 'decimal:2',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // =========================
    // RELATIONSHIPS
    // =========================
    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
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

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['planned', 'in_progress']);
    }

    public function scopeByTooth($query, $toothNumber)
    {
        return $query->where('tooth_number', $toothNumber);
    }

    // =========================
    // STATUS HELPERS
    // =========================
    public static function statuses()
    {
        return [
            'planned'     => 'Planned',
            'in_progress' => 'In Progress',
            'completed'   => 'Completed',
            'cancelled'   => 'Cancelled',
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'planned'     => 'info',
            'in_progress' => 'warning',
            'completed'   => 'success',
            'cancelled'   => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getStatusBadgeAttribute()
    {
        // Tailwind-ready badge
        return '<span class="inline-block px-2 py-1 text-xs font-semibold rounded bg-'
            . $this->status_color . '-500 text-white">'
            . $this->status_text . '</span>';
    }

    // =========================
    // FORMATTED ATTRIBUTES
    // =========================
    public function getFormattedCostAttribute()
    {
        return $this->cost !== null
            ? '৳ ' . number_format((float) $this->cost, 2)
            : 'N/A';
    }


    public function getDurationTextAttribute()
    {
        return $this->duration . ' minutes';
    }

    public function getToothInfoAttribute()
    {
        if (!$this->tooth_number) return 'N/A';

        $info = "Tooth {$this->tooth_number}";
        if ($this->surface) {
            $info .= " ({$this->surface})";
        }

        return $info;
    }

    // =========================
    // STATUS CHECKS
    // =========================
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isPending()
    {
        return in_array($this->status, ['planned', 'in_progress']);
    }

    // =========================
    // STATUS TRANSITIONS
    // =========================
    public function start()
    {
        $this->update(['status' => 'in_progress']);
        return true;
    }

    public function complete($userId = null)
    {
        $this->update([
            'status'       => 'completed',
            'completed_at' => now(),
            'completed_by' => $userId ?? auth()->id(),
        ]);

        // Update treatment actual cost
        $this->treatment->updateActualCost();

        return true;
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);

        // Update treatment actual cost
        $this->treatment->updateActualCost();

        return true;
    }

    // =========================
    // COMMON PROCEDURES
    // =========================
    // In TreatmentProcedure model
    public static function getCommonProcedures()
    {
        return ProcedureCatalog::active()
            ->select('procedure_code', 'procedure_name', 'standard_cost as cost', 'standard_duration as duration')
            ->orderBy('procedure_code')
            ->limit(5)
            ->get() // This returns a Collection
            ->toArray(); // Convert to array for consistency
    }
}
