<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'installment_id',
        'treatment_session_id',
        'allocated_amount',
        'allocation_date',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'allocation_date' => 'datetime',
    ];

    /**
     * Get the payment that owns the allocation
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the installment that owns the allocation
     */
    public function installment(): BelongsTo
    {
        return $this->belongsTo(PaymentInstallment::class, 'installment_id');
    }

    /**
     * Get the treatment session that owns the allocation
     */
    public function treatmentSession(): BelongsTo
    {
        return $this->belongsTo(TreatmentSession::class);
    }

    /**
     * Get the user who created the allocation
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: Allocations for a specific payment
     */
    public function scopeForPayment($query, $paymentId)
    {
        return $query->where('payment_id', $paymentId);
    }

    /**
     * Scope: Allocations for a specific installment
     */
    public function scopeForInstallment($query, $installmentId)
    {
        return $query->where('installment_id', $installmentId);
    }

    /**
     * Scope: Allocations for a specific treatment session
     */
    public function scopeForTreatmentSession($query, $sessionId)
    {
        return $query->where('treatment_session_id', $sessionId);
    }

    /**
     * Get allocation type based on what it's allocated to
     */
    public function getAllocationTypeAttribute(): string
    {
        if ($this->installment_id && $this->treatment_session_id) {
            return 'both';
        } elseif ($this->installment_id) {
            return 'installment';
        } elseif ($this->treatment_session_id) {
            return 'session';
        }
        return 'unknown';
    }

    /**
     * Get description of what this allocation is for
     */
    public function getAllocationDescriptionAttribute(): string
    {
        if ($this->allocation_type === 'both') {
            return "Installment #{$this->installment->installment_number} & Session #{$this->treatmentSession->session_number}";
        } elseif ($this->allocation_type === 'installment') {
            return "Installment #{$this->installment->installment_number}";
        } elseif ($this->allocation_type === 'session') {
            return "Session #{$this->treatmentSession->session_number}";
        }
        return 'Unknown Allocation';
    }
}
