<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAllocation extends Model
{
    use HasFactory;

    // Fillable fields for mass assignment
    protected $fillable = [
        'payment_id',
        'installment_id',
        'treatment_session_id',
        'allocated_amount',
        'allocation_date',
        'notes',
        'created_by'
    ];

    // Casts for automatic type conversion
    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'allocation_date' => 'datetime',
    ];

    /*-----------------------------------
     | Relationships
     *-----------------------------------*/

    // Payment this allocation belongs to
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    // Installment this allocation is applied to
    public function installment(): BelongsTo
    {
        return $this->belongsTo(PaymentInstallment::class, 'installment_id');
    }

    // Treatment session this allocation is applied to
    public function treatmentSession(): BelongsTo
    {
        return $this->belongsTo(TreatmentSession::class);
    }

    // User who created this allocation
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*-----------------------------------
     | Scopes
     *-----------------------------------*/

    // Filter allocations by payment
    public function scopeForPayment($query, $paymentId)
    {
        return $query->where('payment_id', $paymentId);
    }

    // Filter allocations by installment
    public function scopeForInstallment($query, $installmentId)
    {
        return $query->where('installment_id', $installmentId);
    }

    // Filter allocations by treatment session
    public function scopeForTreatmentSession($query, $sessionId)
    {
        return $query->where('treatment_session_id', $sessionId);
    }

    /*-----------------------------------
     | Accessors
     *-----------------------------------*/

    // Determine allocation type
    public function getAllocationTypeAttribute(): string
    {
        if ($this->installment_id && $this->treatment_session_id) {
            return 'both';
        }
        if ($this->installment_id) {
            return 'installment';
        }
        if ($this->treatment_session_id) {
            return 'session';
        }
        return 'unknown';
    }

    // Describe allocation for display
    public function getAllocationDescriptionAttribute(): string
    {
        switch ($this->allocation_type) {
            case 'both':
                return "Installment #{$this->installment->installment_number} & Session #{$this->treatmentSession->session_number}";
            case 'installment':
                return "Installment #{$this->installment->installment_number}";
            case 'session':
                return "Session #{$this->treatmentSession->session_number}";
            default:
                return 'Unknown Allocation';
        }
    }

    // Tailwind badge helper for allocation type
    public function getAllocationBadgeAttribute(): string
    {
        $colors = [
            'installment' => 'bg-blue-500',
            'session' => 'bg-green-500',
            'both' => 'bg-purple-500',
            'unknown' => 'bg-gray-500'
        ];

        return "<span class='badge {$colors[$this->allocation_type]} text-white px-2 py-1 rounded'>"
            . ucfirst($this->allocation_type) .
            "</span>";
    }
}
