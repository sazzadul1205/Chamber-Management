<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_no',
        'invoice_id',
        'patient_id',
        'installment_id',
        'is_advance',
        'for_treatment_session_id',
        'payment_date',
        'payment_method',
        'payment_type',
        'amount',
        'reference_no',
        'card_last_four',
        'bank_name',
        'remarks',
        'status',
        'created_by'
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
        'is_advance' => 'boolean'
    ];

    // Relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function installment()
    {
        return $this->belongsTo(PaymentInstallment::class, 'installment_id');
    }

    public function treatmentSession()
    {
        return $this->belongsTo(TreatmentSession::class, 'for_treatment_session_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function allocations()
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function getHasReceiptAttribute(): bool
    {
        return !is_null($this->receipt);
    }

    public function getRemainingAmountAttribute()
    {
        return $this->amount - $this->allocations->sum('allocated_amount');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    public function scopeForInvoice($query, $invoiceId)
    {
        return $query->where('invoice_id', $invoiceId);
    }

    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'badge bg-warning',
            'completed' => 'badge bg-success',
            'cancelled' => 'badge bg-danger',
            'refunded' => 'badge bg-secondary'
        ];

        $labels = [
            'pending' => 'Pending',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded'
        ];

        return '<span class="' . ($badges[$this->status] ?? 'badge bg-secondary') . '">' .
            ($labels[$this->status] ?? ucfirst($this->status)) . '</span>';
    }

    public function getPaymentMethodBadgeAttribute()
    {
        $badges = [
            'cash' => 'badge bg-success',
            'card' => 'badge bg-primary',
            'bank_transfer' => 'badge bg-info',
            'cheque' => 'badge bg-warning',
            'mobile_banking' => 'badge bg-purple',
            'other' => 'badge bg-secondary'
        ];

        $labels = [
            'cash' => 'Cash',
            'card' => 'Card',
            'bank_transfer' => 'Bank Transfer',
            'cheque' => 'Cheque',
            'mobile_banking' => 'Mobile Banking',
            'other' => 'Other'
        ];

        return '<span class="' . ($badges[$this->payment_method] ?? 'badge bg-secondary') . '">' .
            ($labels[$this->payment_method] ?? ucfirst(str_replace('_', ' ', $this->payment_method))) . '</span>';
    }

    public function getPaymentTypeBadgeAttribute()
    {
        $badges = [
            'full' => 'badge bg-success',
            'partial' => 'badge bg-warning',
            'advance' => 'badge bg-info',
            'refund' => 'badge bg-danger'
        ];

        $labels = [
            'full' => 'Full Payment',
            'partial' => 'Partial Payment',
            'advance' => 'Advance Payment',
            'refund' => 'Refund'
        ];

        return '<span class="' . ($badges[$this->payment_type] ?? 'badge bg-secondary') . '">' .
            ($labels[$this->payment_type] ?? ucfirst($this->payment_type)) . '</span>';
    }

    public function getIsRefundableAttribute()
    {
        return $this->status == 'completed' &&
            $this->created_at->diffInDays(now()) <= 30; // Refund within 30 days
    }

    public function getFormattedPaymentDateAttribute()
    {
        return $this->payment_date->format('d M Y, h:i A');
    }

    // Methods
    public static function generatePaymentNo()
    {
        $latest = self::withTrashed()->latest()->first();
        $year = date('Y');
        $month = date('m');

        if ($latest) {
            $lastNo = $latest->payment_no;
            if (str_starts_with($lastNo, 'PAY' . $year . $month)) {
                $number = intval(substr($lastNo, 9)) + 1;
                return 'PAY' . $year . $month . str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        }

        return 'PAY' . $year . $month . '0001';
    }

    public function processPayment()
    {
        // Update invoice paid amount
        $this->invoice->addPayment($this->amount);

        // Update installment if applicable
        if ($this->installment_id) {
            $this->installment->addPayment($this->amount);
        }

        // Mark as completed
        $this->status = 'completed';
        $this->save();
    }

    public function refund($reason = 'Refund requested')
    {
        if (!$this->is_refundable) {
            throw new \Exception('Payment is not refundable');
        }

        // Refund invoice payment
        $this->invoice->deductPayment($this->amount);

        // Refund installment if applicable
        if ($this->installment_id) {
            $this->installment->deductPayment($this->amount);
        }

        // Update payment status
        $this->status = 'refunded';
        $this->remarks = ($this->remarks ? $this->remarks . "\n" : '') .
            date('Y-m-d H:i') . ': Refunded - ' . $reason;
        $this->save();

        // Create a refund payment record
        $refundPayment = self::create([
            'payment_no' => self::generatePaymentNo(),
            'invoice_id' => $this->invoice_id,
            'patient_id' => $this->patient_id,
            'payment_date' => now(),
            'payment_method' => $this->payment_method,
            'payment_type' => 'refund',
            'amount' => $this->amount,
            'reference_no' => 'REF-' . $this->payment_no,
            'remarks' => 'Refund for payment ' . $this->payment_no . ' - ' . $reason,
            'status' => 'completed',
            'created_by' => $this->created_by
        ]);

        return $refundPayment;
    }

    public function cancel($reason = 'Payment cancelled')
    {
        if ($this->status != 'pending') {
            throw new \Exception('Only pending payments can be cancelled');
        }

        $this->status = 'cancelled';
        $this->remarks = ($this->remarks ? $this->remarks . "\n" : '') .
            date('Y-m-d H:i') . ': Cancelled - ' . $reason;
        $this->save();
    }

    public function allocateToInstallment($installmentId, $amount, $notes = null)
    {
        // Create payment allocation (we'll create PaymentAllocation model in next package)
        // For now, just update the installment
        $installment = PaymentInstallment::find($installmentId);
        if ($installment) {
            $installment->addPayment($amount);

            // Record allocation in remarks
            $this->remarks = ($this->remarks ? $this->remarks . "\n" : '') .
                date('Y-m-d H:i') . ': Allocated ৳' . number_format($amount, 2) .
                ' to installment ' . $installment->installment_number .
                ($notes ? ' - ' . $notes : '');
            $this->save();
        }
    }
}
