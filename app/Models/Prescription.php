<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    // =========================
    // FILLABLE & CASTS
    // =========================
    protected $fillable = [
        'prescription_code',
        'treatment_id',
        'prescription_date',
        'validity_days',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'prescription_date' => 'date',
        'expiry_date' => 'date',
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    // =========================
    // SCOPES
    // =========================
    public function scopeSearch($query, $search)
    {
        return $query->where('prescription_code', 'like', "%{$search}%")
            ->orWhereHas('treatment.patient', fn($q) => $q->where('full_name', 'like', "%{$search}%"));
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
            ->orWhere(fn($q) => $q->where('status', 'active')->whereDate('expiry_date', '<', now()));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('prescription_date', today());
    }

    // =========================
    // STATUS HELPERS
    // =========================
    public static function statuses()
    {
        return [
            'active' => 'Active',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
            'filled' => 'Filled',
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        // Tailwind-friendly badge colors
        $colors = [
            'active' => 'green',
            'expired' => 'yellow',
            'cancelled' => 'red',
            'filled' => 'blue',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getStatusBadgeAttribute()
    {
        // Tailwind badge: bg-{color}-500 text-white px-2 py-1 rounded
        return '<span class="bg-' . $this->status_color . '-500 text-white px-2 py-1 rounded">'
            . $this->status_text . '</span>';
    }

    // =========================
    // EXPIRY & VALIDITY
    // =========================
    public function getExpiryDateAttribute()
    {
        return Carbon::parse($this->prescription_date)
            ->addDays($this->validity_days);
    }

    public function getIsExpiredAttribute()
    {
        return $this->expiry_date < today() || $this->status === 'expired';
    }

    // =========================
    // ACCESSOR HELPERS
    // =========================
    public function getPatientNameAttribute()
    {
        return $this->treatment->patient->full_name;
    }

    public function getDoctorNameAttribute()
    {
        return $this->treatment->doctor->user->full_name;
    }

    public function getTotalItemsAttribute()
    {
        return $this->items()->count();
    }

    public function getPendingItemsAttribute()
    {
        return $this->items()->where('status', 'pending')->count();
    }

    // =========================
    // STATUS ACTIONS
    // =========================
    public function expire()
    {
        if ($this->status === 'active' && $this->is_expired) {
            $this->update(['status' => 'expired']);
            return true;
        }
        return false;
    }

    public function cancel()
    {
        if ($this->status === 'active') {
            $this->update(['status' => 'cancelled']);
            return true;
        }
        return false;
    }

    public function markAsFilled()
    {
        if ($this->status === 'active') {
            $this->update(['status' => 'filled']);
            return true;
        }
        return false;
    }

    // =========================
    // CODE GENERATION
    // =========================
    public static function generatePrescriptionCode()
    {
        $last = self::orderByDesc('prescription_code')->first();
        $next = $last ? ((int) substr($last->prescription_code, 2)) + 1 : 1;
        return 'RX' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    // =========================
    // SUMMARY OF PRESCRIPTION ITEMS
    // =========================
    public function getPrescriptionSummary()
    {
        return $this->items->map(fn($item) => "{$item->medicine->brand_name} - {$item->dosage} {$item->frequency}")
            ->implode(', ');
    }
}
