<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'medicine_id',
        'dosage',
        'frequency',
        'duration',
        'route',
        'instructions',
        'quantity',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    // Scopes
    public function scopeByPrescription($query, $prescriptionId)
    {
        return $query->where('prescription_id', $prescriptionId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDispensed($query)
    {
        return $query->where('status', 'dispensed');
    }

    // Helper Methods
    public static function routes()
    {
        return [
            'oral' => 'Oral',
            'topical' => 'Topical',
            'injection' => 'Injection',
            'inhalation' => 'Inhalation',
        ];
    }

    public static function statuses()
    {
        return [
            'pending' => 'Pending',
            'dispensed' => 'Dispensed',
            'cancelled' => 'Cancelled',
        ];
    }

    public function getRouteTextAttribute()
    {
        return self::routes()[$this->route] ?? $this->route;
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'dispensed' => 'success',
            'cancelled' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getStatusBadgeAttribute()
    {
        return '<span class="badge bg-' . $this->status_color . '">' . $this->status_text . '</span>';
    }

    public function getDosageInfoAttribute()
    {
        return $this->dosage . ' ' . $this->frequency . ' for ' . $this->duration;
    }

    public function getInstructionsTextAttribute()
    {
        return $this->instructions ?: 'Take as directed';
    }

    public function dispense()
    {
        if ($this->status === 'pending') {
            $this->update(['status' => 'dispensed']);
            return true;
        }
        return false;
    }

    public function cancel()
    {
        if ($this->status === 'pending') {
            $this->update(['status' => 'cancelled']);
            return true;
        }
        return false;
    }

    public function getMedicineInfoAttribute()
    {
        return $this->medicine ?
            "{$this->medicine->brand_name} ({$this->medicine->generic_name}) - {$this->medicine->strength}" :
            'N/A';
    }
}
