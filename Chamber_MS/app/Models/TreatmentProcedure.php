<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentProcedure extends Model
{
    use HasFactory;

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

    protected $casts = [
        'cost' => 'decimal:2',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
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

    // Helper Methods
    public static function statuses()
    {
        return [
            'planned' => 'Planned',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'planned' => 'info',
            'in_progress' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getStatusBadgeAttribute()
    {
        return '<span class="badge bg-' . $this->status_color . '">' . $this->status_text . '</span>';
    }

    public function getFormattedCostAttribute()
    {
        return '৳ ' . number_format($this->cost, 2);
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

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isPending()
    {
        return in_array($this->status, ['planned', 'in_progress']);
    }

    public function start()
    {
        if ($this->status === 'planned') {
            $this->update(['status' => 'in_progress']);
            return true;
        }
        return false;
    }

    public function complete($userId = null)
    {
        if ($this->status !== 'cancelled') {
            $this->update([
                'status' => 'completed',
                'completed_at' => now(),
                'completed_by' => $userId ?? auth()->id(),
            ]);

            // Update treatment actual cost
            $this->treatment->updateActualCost();

            return true;
        }
        return false;
    }

    public function cancel()
    {
        if ($this->isPending()) {
            $this->update(['status' => 'cancelled']);

            // Update treatment actual cost
            $this->treatment->updateActualCost();

            return true;
        }
        return false;
    }

    public static function getCommonProcedures()
    {
        return [
            ['code' => 'FILL-001', 'name' => 'Composite Filling', 'cost' => 1500, 'duration' => 45],
            ['code' => 'FILL-002', 'name' => 'Amalgam Filling', 'cost' => 1000, 'duration' => 40],
            ['code' => 'EXT-001', 'name' => 'Simple Extraction', 'cost' => 800, 'duration' => 30],
            ['code' => 'EXT-002', 'name' => 'Surgical Extraction', 'cost' => 3000, 'duration' => 60],
            ['code' => 'RCT-001', 'name' => 'Root Canal Treatment (Anterior)', 'cost' => 4000, 'duration' => 90],
            ['code' => 'RCT-002', 'name' => 'Root Canal Treatment (Posterior)', 'cost' => 6000, 'duration' => 120],
            ['code' => 'CROWN-001', 'name' => 'PFM Crown', 'cost' => 5000, 'duration' => 120],
            ['code' => 'CROWN-002', 'name' => 'Zirconia Crown', 'cost' => 8000, 'duration' => 120],
            ['code' => 'SCAL-001', 'name' => 'Scaling & Polishing', 'cost' => 1000, 'duration' => 45],
            ['code' => 'WHIT-001', 'name' => 'Teeth Whitening', 'cost' => 8000, 'duration' => 60],
        ];
    }
}
