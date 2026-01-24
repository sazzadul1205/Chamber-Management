<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInstallment extends Model
{
    use HasFactory;

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

    protected $casts = [
        'due_date' => 'date',
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'late_fee_amount' => 'decimal:2',
        'late_fee_applied' => 'boolean',
        'reminder_sent_date' => 'date'
    ];

    // Relationships
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

    // Scopes
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

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'badge bg-secondary',
            'partial' => 'badge bg-warning',
            'paid' => 'badge bg-success',
            'overdue' => 'badge bg-danger',
            'cancelled' => 'badge bg-dark'
        ];

        $labels = [
            'pending' => 'Pending',
            'partial' => 'Partial',
            'paid' => 'Paid',
            'overdue' => 'Overdue',
            'cancelled' => 'Cancelled'
        ];

        return '<span class="' . ($badges[$this->status] ?? 'badge bg-secondary') . '">' .
            ($labels[$this->status] ?? ucfirst($this->status)) . '</span>';
    }

    public function getBalanceAttribute()
    {
        return $this->amount_due - $this->amount_paid;
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date < now() && in_array($this->status, ['pending', 'partial']);
    }

    public function getDaysOverdueAttribute()
    {
        if (!$this->is_overdue) {
            return 0;
        }

        return now()->diffInDays($this->due_date);
    }

    public function getPaymentProgressAttribute()
    {
        if ($this->amount_due == 0) {
            return 0;
        }

        return ($this->amount_paid / $this->amount_due) * 100;
    }

    public function getTotalDueWithLateFeeAttribute()
    {
        return $this->amount_due + $this->late_fee_amount;
    }

    // Methods
    public function addPayment($amount)
    {
        $this->amount_paid += $amount;

        // Update status
        if ($this->amount_paid >= $this->amount_due) {
            $this->status = 'paid';
        } elseif ($this->amount_paid > 0) {
            $this->status = 'partial';
        }

        $this->save();

        // Update invoice paid amount
        $this->invoice->addPayment($amount);
    }

    public function deductPayment($amount)
    {
        $this->amount_paid = max(0, $this->amount_paid - $amount);

        // Update status
        if ($this->amount_paid == 0) {
            $this->status = 'pending';
        } elseif ($this->amount_paid < $this->amount_due) {
            $this->status = 'partial';
        }

        $this->save();

        // Update invoice paid amount
        $this->invoice->deductPayment($amount);
    }

    public function checkAndUpdateStatus()
    {
        // Check if overdue
        if ($this->due_date < now() && in_array($this->status, ['pending', 'partial'])) {
            $this->status = 'overdue';
            $this->save();
            return true;
        }

        return false;
    }

    public function applyLateFee($feeAmount, $reason = 'Late payment fee')
    {
        $this->late_fee_applied = true;
        $this->late_fee_amount = $feeAmount;
        $this->notes = ($this->notes ? $this->notes . "\n" : '') .
            date('Y-m-d') . ': ' . $reason . ' - ৳' . number_format($feeAmount, 2);
        $this->save();

        // Update invoice total (you might want to create a separate transaction for this)
        // $this->invoice->total_amount += $feeAmount;
        // $this->invoice->save();
    }

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
