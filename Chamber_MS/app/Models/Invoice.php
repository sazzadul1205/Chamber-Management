<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_no',
        'patient_id',
        'treatment_id',
        'appointment_id',
        'invoice_date',
        'due_date',
        'payment_plan',
        'advance_amount',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'discount_percent',
        'total_amount',
        'paid_amount',
        'balance_amount',
        'payment_terms',
        'status',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'advance_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2'
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function installments()
    {
        return $this->hasMany(PaymentInstallment::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('invoice_date', [$startDate, $endDate]);
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('due_date')
            ->where('due_date', '<=', now()->addDays($days))
            ->where('due_date', '>', now());
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'badge bg-secondary',
            'sent' => 'badge bg-info',
            'partial' => 'badge bg-warning',
            'paid' => 'badge bg-success',
            'cancelled' => 'badge bg-danger',
            'overdue' => 'badge bg-dark'
        ];

        $labels = [
            'draft' => 'Draft',
            'sent' => 'Sent',
            'partial' => 'Partial',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled',
            'overdue' => 'Overdue'
        ];

        return '<span class="' . ($badges[$this->status] ?? 'badge bg-secondary') . '">' .
            ($labels[$this->status] ?? ucfirst($this->status)) . '</span>';
    }

    public function getPaymentPlanBadgeAttribute()
    {
        $badges = [
            'full' => 'badge bg-success',
            'installment' => 'badge bg-primary'
        ];

        return '<span class="' . ($badges[$this->payment_plan] ?? 'badge bg-secondary') . '">' .
            ucfirst($this->payment_plan) . '</span>';
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date && $this->due_date < now() &&
            in_array($this->status, ['sent', 'partial']);
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
        if ($this->total_amount == 0) {
            return 0;
        }

        return ($this->paid_amount / $this->total_amount) * 100;
    }

    // Methods
    public static function generateInvoiceNo()
    {
        $latest = self::withTrashed()->latest()->first();
        $year = date('Y');
        $month = date('m');

        if ($latest) {
            $lastNo = $latest->invoice_no;
            if (str_starts_with($lastNo, 'INV' . $year . $month)) {
                $number = intval(substr($lastNo, 9)) + 1;
                return 'INV' . $year . $month . str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        }

        return 'INV' . $year . $month . '0001';
    }

    public function updateBalance()
    {
        $this->balance_amount = $this->total_amount - $this->paid_amount;

        // Update status based on balance
        if ($this->balance_amount <= 0 && $this->total_amount > 0) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0 && $this->balance_amount > 0) {
            $this->status = 'partial';
        } elseif ($this->status == 'draft' && $this->subtotal > 0) {
            $this->status = 'sent';
        }

        // Check if overdue
        if (
            $this->due_date && $this->due_date < now() &&
            in_array($this->status, ['sent', 'partial'])
        ) {
            $this->status = 'overdue';
        }

        $this->save();
    }

    public function calculateTotals()
    {
        $subtotal = $this->items()->sum('total_amount');
        $discountAmount = ($this->discount_percent / 100) * $subtotal;

        $this->subtotal = $subtotal;
        $this->discount_amount = $discountAmount;
        $this->total_amount = $subtotal - $discountAmount + $this->tax_amount;
        $this->updateBalance();
    }

    public function addPayment($amount)
    {
        $this->paid_amount += $amount;
        $this->updateBalance();
    }

    public function deductPayment($amount)
    {
        $this->paid_amount = max(0, $this->paid_amount - $amount);
        $this->updateBalance();
    }
}
