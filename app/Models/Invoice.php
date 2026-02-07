<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    // Fillable fields for mass assignment
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

    // Attribute casting for proper data types
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
    public function patient() { return $this->belongsTo(Patient::class); }
    public function treatment() { return $this->belongsTo(Treatment::class); }
    public function appointment() { return $this->belongsTo(Appointment::class); }
    public function items() { return $this->hasMany(InvoiceItem::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function installments() { return $this->hasMany(PaymentInstallment::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function updatedBy() { return $this->belongsTo(User::class, 'updated_by'); }

    // Scopes for easy filtering
    public function scopeDraft($query) { return $query->where('status', 'draft'); }
    public function scopeSent($query) { return $query->where('status', 'sent'); }
    public function scopePaid($query) { return $query->where('status', 'paid'); }
    public function scopePartial($query) { return $query->where('status', 'partial'); }
    public function scopeOverdue($query) { return $query->where('status', 'overdue'); }
    public function scopeCancelled($query) { return $query->where('status', 'cancelled'); }
    public function scopeForPatient($query, $patientId) { return $query->where('patient_id', $patientId); }
    public function scopeBetweenDates($query, $startDate, $endDate) { return $query->whereBetween('invoice_date', [$startDate, $endDate]); }

    // Get invoices due soon (default next 7 days)
    public function scopeDueSoon($query, $days = 7)
    {
        return $query->whereNotIn('status', ['paid', 'cancelled'])
            ->whereNotNull('due_date')
            ->where('due_date', '<=', now()->addDays($days))
            ->where('due_date', '>', now());
    }

    // Accessors for Tailwind badge UI
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'bg-gray-400 text-white',
            'sent' => 'bg-blue-400 text-white',
            'partial' => 'bg-yellow-400 text-black',
            'paid' => 'bg-green-500 text-white',
            'cancelled' => 'bg-red-500 text-white',
            'overdue' => 'bg-gray-800 text-white'
        ];

        $label = ucfirst($this->status);

        return '<span class="px-2 py-1 rounded ' . ($badges[$this->status] ?? 'bg-gray-400') . '">' . $label . '</span>';
    }

    public function getPaymentPlanBadgeAttribute()
    {
        $badges = [
            'full' => 'bg-green-500 text-white',
            'installment' => 'bg-blue-500 text-white'
        ];

        return '<span class="px-2 py-1 rounded ' . ($badges[$this->payment_plan] ?? 'bg-gray-400') . '">' . ucfirst($this->payment_plan) . '</span>';
    }

    // Determine if invoice is overdue
    public function getIsOverdueAttribute()
    {
        return $this->due_date && $this->due_date < now();
    }

    // Calculate days overdue
    public function getDaysOverdueAttribute()
    {
        return $this->is_overdue ? now()->diffInDays($this->due_date) : 0;
    }

    // Calculate payment progress percentage
    public function getPaymentProgressAttribute()
    {
        return $this->total_amount > 0 ? ($this->paid_amount / $this->total_amount) * 100 : 0;
    }

    // Generate unique invoice number
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

    // Update balance and status
    public function updateBalance()
    {
        $this->balance_amount = $this->total_amount - $this->paid_amount;

        // Auto-update status
        if ($this->balance_amount <= 0 && $this->total_amount > 0) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0 && $this->balance_amount > 0) {
            $this->status = 'partial';
        } elseif ($this->subtotal > 0) {
            $this->status = 'sent';
        }

        if ($this->is_overdue) {
            $this->status = 'overdue';
        }

        $this->save();
    }

    // Calculate subtotal, discount, and total
    public function calculateTotals()
    {
        $subtotal = $this->items()->sum('total_amount');
        $discountAmount = ($this->discount_percent / 100) * $subtotal;

        $this->subtotal = $subtotal;
        $this->discount_amount = $discountAmount;
        $this->total_amount = $subtotal - $discountAmount + $this->tax_amount;

        $this->updateBalance();
    }

    // Payment handling
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
