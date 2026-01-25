<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    use HasFactory;

    /**
     * Mass-assignable attributes
     */
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

    /**
     * Attribute casting
     */
    protected $casts = [
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==================================================
    // RELATIONSHIPS
    // ==================================================

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    // ==================================================
    // QUERY SCOPES
    // ==================================================

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

    // ==================================================
    // STATIC CONFIG (ROUTES & STATUSES)
    // ==================================================

    public static function routes()
    {
        return [
            'oral'        => 'Oral',
            'topical'     => 'Topical',
            'injection'   => 'Injection',
            'inhalation'  => 'Inhalation',
        ];
    }

    public static function statuses()
    {
        return [
            'pending'   => 'Pending',
            'dispensed' => 'Dispensed',
            'cancelled' => 'Cancelled',
        ];
    }

    // ==================================================
    // ACCESSORS (DISPLAY HELPERS)
    // ==================================================

    public function getRouteTextAttribute()
    {
        return self::routes()[$this->route];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status];
    }

    /**
     * Bootstrap / semantic status color
     * (Map to Tailwind in Blade)
     */
    public function getStatusColorAttribute()
    {
        return [
            'pending'   => 'warning',
            'dispensed' => 'success',
            'cancelled' => 'danger',
        ][$this->status];
    }

    /**
     * Raw HTML badge (legacy / Bootstrap)
     * ⚠️ Prefer Tailwind badge in Blade
     */
    public function getStatusBadgeAttribute()
    {
        return '<span class="badge bg-' . $this->status_color . '">' . $this->status_text . '</span>';
    }

    public function getDosageInfoAttribute()
    {
        return "{$this->dosage} {$this->frequency} for {$this->duration}";
    }

    public function getInstructionsTextAttribute()
    {
        return $this->instructions ?: 'Take as directed';
    }

    public function getMedicineInfoAttribute()
    {
        return "{$this->medicine->brand_name} ({$this->medicine->generic_name}) - {$this->medicine->strength}";
    }

    // ==================================================
    // STATE ACTIONS
    // ==================================================

    public function dispense(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update(['status' => 'dispensed']);
        return true;
    }

    public function cancel(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update(['status' => 'cancelled']);
        return true;
    }
}
