<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

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

    // Relationships
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

    // Scopes
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('prescription_code', 'like', "%{$search}%")
                ->orWhereHas('treatment', function ($treatmentQuery) use ($search) {
                    $treatmentQuery->whereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('full_name', 'like', "%{$search}%");
                    });
                });
        });
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
            ->orWhere(function ($q) {
                $q->where('status', 'active')
                    ->whereDate('expiry_date', '<', now());
            });
    }

    public function scopeToday($query)
    {
        return $query->whereDate('prescription_date', today());
    }

    // Helper Methods
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
        $colors = [
            'active' => 'success',
            'expired' => 'warning',
            'cancelled' => 'danger',
            'filled' => 'info',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getStatusBadgeAttribute()
    {
        return '<span class="badge bg-' . $this->status_color . '">' . $this->status_text . '</span>';
    }

    public function getExpiryDateAttribute()
    {
        return $this->prescription_date->addDays($this->validity_days);
    }

    public function getIsExpiredAttribute()
    {
        return $this->expiry_date < today() || $this->status === 'expired';
    }

    public function getPatientNameAttribute()
    {
        return $this->treatment->patient->full_name ?? 'N/A';
    }

    public function getDoctorNameAttribute()
    {
        return $this->treatment->doctor->user->full_name ?? 'N/A';
    }

    public function getTotalItemsAttribute()
    {
        return $this->items()->count();
    }

    public function getPendingItemsAttribute()
    {
        return $this->items()->where('status', 'pending')->count();
    }

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

    public static function generatePrescriptionCode()
    {
        $last = self::orderByDesc('prescription_code')->first();
        $next = $last ? ((int) substr($last->prescription_code, 2)) + 1 : 1;
        return 'RX' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    public function getPrescriptionSummary()
    {
        $summary = [];
        foreach ($this->items as $item) {
            $summary[] = $item->medicine->brand_name . ' - ' . $item->dosage . ' ' . $item->frequency;
        }
        return implode(', ', $summary);
    }
}
