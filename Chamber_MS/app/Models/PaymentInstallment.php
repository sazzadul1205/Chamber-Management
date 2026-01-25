<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInstallment extends Model
{
    use HasFactory;

    // Mass assignable fields
    protected $fillable = [
        'invoice_id',
        'installment_number',
        'description',
        'due_date',
        'amount_due',
        'amount_paid',
        'status',
        'late_fee_applied',
        'late_fee_amount',
        'reminder_sent_date',
        'notes',
        'created_by'
    ];

    // Casts for proper data types
    protected $casts = [
        'due_date' => 'date',
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'late_fee_amount' => 'decimal:2',
        'late_fee_applied' => 'boolean',
        'reminder_sent_date' => 'date'
    ];

    /*================================
     | Relationships
     *================================*/
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'installment_id');
    }

    public function paymentAllocations()
    {
        return $this->hasMany(PaymentAllocation::class, 'installment_id');
    }

    /*================================
     | Accessors
     *================================*/

    // Remaining balance for this installment
    public function getRemainingBalanceAttribute()
    {
        return $this->amount_due - $this->amount_paid;
    }

    // Badge for status (Tailwind-friendly)
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'badge bg-gray-400',
            'partial' => 'badge bg-yellow-400',
            'paid' => 'badge bg-green-500',
            'overdue' => 'badge bg-red-500',
            'cancelled' => 'badge bg-gray-700'
        ];

        return '<span class="' . ($badges[$this->status] ?? 'badge bg-gray-400') . '">' .
            ucfirst($this->status) . '</span>';
    }

    // Check if overdue
    public function getIsOverdueAttribute()
    {
        return $this->due_date < now() && in_array($this->status, ['pending', 'partial']);
    }

    // Days overdue
    public function getDaysOverdueAttribute()
    {
        return $this->is_overdue ? now()->diffInDays($this->due_date) : 0;
    }

    // Payment progress in percentage
    public function getPaymentProgressAttribute()
    {
        return $this->amount_due == 0 ? 0 : ($this->amount_paid / $this->amount_due) * 100;
    }

    // Total due including late fees
    public function getTotalDueWithLateFeeAttribute()
    {
        return $this->amount_due + $this->late_fee_amount;
    }

    /*================================
     | Scopes
     *================================*/
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->whereIn('status', ['pending', 'partial'])
            ->where('due_date', '<=', now()->addDays($days))
            ->where('due_date', '>', now());
    }

    public function scopeOverdueItems($query)
    {
        return $query->whereIn('status', ['pending', 'partial'])
            ->where('due_date', '<', now());
    }

    /*================================
     | Methods
     *================================*/

    // Add a payment to this installment
    public function addPayment($amount)
    {
        $this->amount_paid += $amount;

        if ($this->amount_paid >= $this->amount_due) {
            $this->status = 'paid';
        } elseif ($this->amount_paid > 0) {
            $this->status = 'partial';
        }

        $this->save();

        // Sync with invoice
        $this->invoice->addPayment($amount);
    }

    // Deduct a payment (refund)
    public function deductPayment($amount)
    {
        $this->amount_paid = max(0, $this->amount_paid - $amount);

        if ($this->amount_paid == 0) {
            $this->status = 'pending';
        } elseif ($this->amount_paid < $this->amount_due) {
            $this->status = 'partial';
        }

        $this->save();

        // Sync with invoice
        $this->invoice->deductPayment($amount);
    }

    // Check if installment is overdue and update status
    public function checkAndUpdateStatus()
    {
        if ($this->due_date < now() && in_array($this->status, ['pending', 'partial'])) {
            $this->status = 'overdue';
            $this->save();
            return true;
        }
        return false;
    }

    // Apply a late fee to this installment
    public function applyLateFee($feeAmount, $reason = 'Late payment fee')
    {
        $this->late_fee_applied = true;
        $this->late_fee_amount = $feeAmount;
        $this->notes = ($this->notes ? $this->notes . "\n" : '') .
            date('Y-m-d') . ": {$reason} - ৳" . number_format($feeAmount, 2);
        $this->save();
    }

    // Static helper to mark overdue installments
    public static function checkOverdueInstallments()
    {
        $overdue = self::overdueItems()->get();

        foreach ($overdue as $installment) {
            $installment->status = 'overdue';
            $installment->save();
        }

        return $overdue->count();
    }
}
